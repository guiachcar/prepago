<template>
  <AppLayout @search="handleSearch">
    <template #sidebar>
      <div class="flex flex-col h-full">
        <div class="flex justify-between items-center p-4 border-b">
          <h3 class="font-medium">Histórico de Consultas</h3>
          <button 
            @click="refreshList" 
            class="text-sm text-indigo-600 hover:text-indigo-800"
          >
            <span class="flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </span>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto">
          <div v-if="filteredSearches.length === 0" class="p-4 text-center text-gray-500">
            Nenhuma consulta encontrada
          </div>
          <ClientListItem
            v-for="search in filteredSearches"
            :key="search.id"
            :client="search"
            :is-selected="selectedSearch?.id === search.id"
            @select="selectSearch(search)"
          />
        </div>

        <div class="p-4 border-t">
          <button
            @click="startNewSearch"
            class="w-full py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Consulta
          </button>
        </div>
      </div>
    </template>

    <div class="flex-1 p-6 bg-gray-100">
      <div v-if="selectedSearch" class="mb-6">
        <div v-if="selectedSearch.details && selectedSearch.details.length" class="grid grid-cols-1 gap-6">
          <h2 class="text-xl font-semibold text-gray-900">Detalhes da Consulta</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="detail in selectedSearch.details" :key="detail.document" class="bg-white rounded-lg shadow p-4 text-sm">
              <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-medium flex items-center">
                  Documento: 
                  <span @click="copyToClipboard(detail.document)" class="ml-2 cursor-pointer text-indigo-600 hover:text-indigo-800">
                    {{ detail.document }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2h-2M8 4H6a2 2 0 00-2 2v14a2 2 0 002 2h2m4-18h4m-4 0v18" />
                    </svg>
                  </span>
                </h3>
                <span :class="getStatusClass(detail.status)">
                  {{ detail.status }}
                </span>
              </div>
              <p class="text-sm text-gray-600"><strong>Data de Emissão:</strong> <span class="font-semibold">{{ formatDate(detail.created_at) }}</span></p>
              <p><strong>Serviço:</strong> {{ detail.service }}</p>
              <p><strong>Mensagem:</strong> {{ detail.message }}</p>
              <p v-if="detail.result?.[0]?.emissao_data"><strong>Data de Emissão:</strong> {{ detail.result[0]?.emissao_data }}</p>
              <p v-if="detail.result?.[0]?.validade"><strong>Validade:</strong> {{ detail.result[0]?.validade }}</p>
              <p v-if="detail.url_certificate">
                <a :href="detail.url_certificate" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                  Ver Certidão
                </a>
              </p>

              <div v-if="Array.isArray(detail.result) && detail.result.length">
                <h4 class="font-medium mt-2">Resultados:</h4>
                <ul class="list-disc pl-5">
                  <li v-for="(item, index) in detail.result" :key="index">
                    <p><strong>Nome:</strong> {{ item.nome || item.razao_social }}</p>
                    <p><strong>Documento:</strong> {{ item.cpf || item.cnpj }}</p>
                    <p v-if="item.site_receipt"><strong>Site de Comprovante:</strong>
                      <a :href="item.site_receipt" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                        Ver Comprovante
                      </a>
                    </p>
                    <p v-if="item.telefone"><strong>Telefone:</strong> {{ item.telefone }}</p>
                    <p v-if="item.endereco_logradouro"><strong>Endereço:</strong> {{ item.endereco_logradouro }}, {{ item.endereco_numero }} - {{ item.endereco_bairro }}, {{ item.endereco_municipio }} - {{ item.endereco_uf }}</p>
                    <p v-if="item.capital_social"><strong>Capital Social:</strong> {{ item.capital_social }}</p>
                    <p v-if="item.atividade_economica"><strong>Atividade Econômica:</strong> {{ item.atividade_economica }}</p>
                  </li>
                </ul>
              </div>

              <div v-else-if="detail.result && typeof detail.result === 'object'">
                <h4 class="font-medium mt-2">Detalhes dos Protestos:</h4>
                <p><strong>Constam Protestos:</strong> {{ detail.result.constamProtestos ? 'Sim' : 'Não' }}</p>
                <p><strong>Data da Consulta:</strong> {{ detail.result.dataConsulta }}</p>
                <p><strong>Total de Protestos:</strong> {{ detail.result.totalNumProtestos }}</p>
                <p><strong>Observações:</strong> {{ detail.result.observacoes }}</p>

                <div v-if="detail.result.protestos && detail.result.protestos.length">
                  <h5 class="font-medium mt-2">Protestos:</h5>
                  <ul class="list-disc pl-5">
                    <li v-for="(protesto, index) in detail.result.protestos" :key="index">
                      <p><strong>Estado:</strong> {{ protesto.estado }}</p>
                      <p><strong>Total de Protestos no Estado:</strong> {{ protesto.totalNumProtestosUf }}</p>
                      <div v-if="protesto.cartoriosProtesto && protesto.cartoriosProtesto.length">
                        <h6 class="font-medium mt-2">Cartórios de Protesto:</h6>
                        <ul class="list-disc pl-5">
                          <li v-for="(cartorio, index) in protesto.cartoriosProtesto" :key="index">
                            <p><strong>Nome:</strong> {{ cartorio.nome }}</p>
                            <p><strong>Cidade:</strong> {{ cartorio.cidade }}</p>
                            <p><strong>Endereço:</strong> {{ cartorio.endereco }}</p>
                            <p><strong>Telefone:</strong> {{ cartorio.telefone }}</p>
                            <p><strong>Número de Protestos:</strong> {{ cartorio.numProtestos }}</p>
                            <div v-if="cartorio.protesto && cartorio.protesto.length">
                              <h6 class="font-medium mt-2">Detalhes dos Protestos:</h6>
                              <ul class="list-disc pl-5">
                                <li v-for="(detalheProtesto, index) in cartorio.protesto" :key="index">
                                  <p><strong>Valor Protestado:</strong> {{ detalheProtesto.valorProtestado }}</p>
                                </li>
                              </ul>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else-if="!selectedSearch">
        <form @submit.prevent="submitSearch" class="bg-white shadow rounded-lg p-6">
          <div class="space-y-6">
            <div>
              <h3 class="text-lg font-medium text-gray-900">Nova Consulta</h3>
              <p class="mt-1 text-sm text-gray-500">Preencha os dados para realizar uma nova consulta.</p>
            </div>

            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700">
                  Documento (CPF/CNPJ)
                </label>
                <input
                  v-model="form.document"
                  type="text"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                />
              </div>

              <!-- Exibe data de nascimento somente se for CPF -->
              <div v-if="isCPF">
                <label class="block text-sm font-medium text-gray-700">
                  Data de Nascimento
                </label>
                <input
                  v-model="form.birthdate"
                  type="date"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <!-- Sempre exibir as opções de serviços -->
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <label class="block text-sm font-medium text-gray-700">
                    Selecione os Serviços
                  </label>
                  <button
                    type="button"
                    @click="markAllServices"
                    class="text-sm text-indigo-600 hover:text-indigo-800 underline"
                  >
                    Marcar Todas
                  </button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <label v-for="service in availableServices" :key="service.value" class="flex items-center space-x-3">
                    <input
                      type="checkbox"
                      v-model="form.services"
                      :value="service.value"
                      class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="text-sm text-gray-700">{{ service.label }}</span>
                  </label>
                </div>
              </div>

              <!-- Mostrar opções do TRF se 'federal-court' estiver selecionado
                   e NÃO estiverem todas marcadas -->
              <div v-if="shouldShowTRFOptions" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Região do TRF
                  </label>
                  <select
                    v-model="form.region"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  >
                    <option value="">Selecione a região</option>
                    <option v-for="region in federalRegions" :key="region.value" :value="region.value">
                      {{ region.label }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Tipo de Certidão (TRF)
                  </label>
                  <select
                    v-model="form.certificateType"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  >
                    <option value="">Selecione o tipo</option>
                    <option value="0">Certidão Criminal e Cível</option>
                    <option value="1">Certidão Criminal</option>
                    <option value="2">Certidão Cível</option>
                  </select>
                </div>
              </div>

              <div v-if="shouldShowTRTOptions" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">
                    Região do TRT
                  </label>
                </div>
                <select
                  v-model="form.labourRegion"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                >
                  <option value="">Selecione a região</option>
                  <option v-for="region in labourRegions" :key="region.value" :value="region.value">
                    {{ region.label }}
                  </option>
                </select>
              </div>

            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="resetForm"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Limpar
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
              >
                {{ form.processing ? 'Processando...' : 'Consultar' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal para exibir erros -->
    <div v-if="showErrorModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-medium">Erros</h3>
        <div v-if="normalErrors.length">
          <h4 class="font-medium mt-4">Erros Normais:</h4>
          <ul>
            <li v-for="(error, index) in normalErrors" :key="index">{{ error }}</li>
          </ul>
        </div>
        <div v-if="validatorErrors.length">
          <h4 class="font-medium mt-4">Erros de Validação:</h4>
          <ul>
            <li v-for="(error, index) in validatorErrors" :key="index">{{ error }}</li>
          </ul>
        </div>
        <button @click="closeErrorModal" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Fechar</button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ClientListItem from '@/Components/ClientListItem.vue'

const props = defineProps({
  certificates: {
    type: Array,
    default: () => []
  },
  results: {
    type: Object,
    default: () => null
  },
  documents: {
    type: Object,
    default: () => ({})
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const searchQuery = ref('')
const selectedSearch = ref(null)

const form = useForm({
  document: '',
  birthdate: '',
  services: [],
  region: '',
  labourRegion: '',
  certificateType: '',
})

const availableServices = [
  { value: 'federal-court', label: 'Tribunal Federal (TRF)' },
  { value: 'labour-court', label: 'Tribunal do Trabalho (TRT)' },
  { value: 'protests', label: 'Protestos' },
  { value: 'receita-federal', label: 'Receita Federal' },
  { value: 'debt-certificate', label: 'Certidão de Débitos' },
  { value: 'cndt', label: 'CNDT' }
]

const federalRegions = [
  { value: 'TRF1', label: 'TRF1 - 1ª Região (AC, AM, AP, BA, DF, GO, MA, MT, PA, PI, RO, RR, TO)' },
  { value: 'TRF2', label: 'TRF2 - 2ª Região (RJ, ES)' },
  { value: 'TRF3', label: 'TRF3 - 3ª Região (SP, MS)' },
  { value: 'TRF4', label: 'TRF4 - 4ª Região (RS, SC, PR)' },
  { value: 'TRF5', label: 'TRF5 - 5ª Região (AL, CE, PB, PE, RN, SE)' },
  { value: 'TRF6', label: 'TRF6 - 6ª Região (MG)' }
]

const labourRegions = [
  { value: 1, label: 'TRT1 - Rio de Janeiro' },
  { value: 2, label: 'TRT2 - São Paulo (Capital)' },
  { value: 3, label: 'TRT3 - Minas Gerais' },
  { value: 4, label: 'TRT4 - Rio Grande do Sul' },
  { value: 5, label: 'TRT5 - Bahia' },
  { value: 6, label: 'TRT6 - Pernambuco' },
  { value: 7, label: 'TRT7 - Ceará' },
  { value: 8, label: 'TRT8 - Pará e Amapá' },
  { value: 9, label: 'TRT9 - Paraná' },
  { value: 10, label: 'TRT10 - Distrito Federal e Tocantins' },
  { value: 11, label: 'TRT11 - Amazonas e Roraima' },
  { value: 12, label: 'TRT12 - Santa Catarina' },
  { value: 13, label: 'TRT13 - Paraíba' },
  { value: 14, label: 'TRT14 - Rondônia e Acre' },
  { value: 15, label: 'TRT15 - São Paulo (Interior)' },
  { value: 16, label: 'TRT16 - Maranhão' },
  { value: 17, label: 'TRT17 - Espírito Santo' },
  { value: 18, label: 'TRT18 - Goiás' },
  { value: 19, label: 'TRT19 - Alagoas' },
  { value: 20, label: 'TRT20 - Sergipe' },
  { value: 21, label: 'TRT21 - Rio Grande do Norte' },
  { value: 22, label: 'TRT22 - Piauí' },
  { value: 23, label: 'TRT23 - Mato Grosso' },
  { value: 24, label: 'TRT24 - Mato Grosso do Sul' }
]

const previousSearches = computed(() => {
  if (props.documents) {
    return Object.entries(props.documents).map(([document, certificates]) => ({
      document,
      ...certificates[0],
      allCertificates: certificates
    }))
  }
  return []
})

const filteredSearches = computed(() => {
  if (!searchQuery.value) {
    return previousSearches.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return previousSearches.value.filter(search => {
    return search.document.toLowerCase().includes(query) ||
           getServicesText(search.services).toLowerCase().includes(query) ||
           search.status.toLowerCase().includes(query)
  })
})

const isCPF = computed(() => {
  const numbers = form.document.replace(/\D/g, '')
  return numbers.length === 11
})

const shouldShowTRFOptions = computed(() => {
  return form.services.includes('federal-court');
})

const shouldShowTRTOptions = computed(() => {
  return form.services.includes('labour-court');
})

const handleSearch = (query) => {
  searchQuery.value = query
}

async function selectSearch(search) {
    selectedSearch.value = {
        ...search,
        services: Array.isArray(search.services) 
            ? search.services 
            : JSON.parse(search.services || '[]'),
    };
  
    const responseDetails = await fetch(`/api/documents/${search.document}`);
    const details = await responseDetails.json();
    selectedSearch.value.details = details;
}

const closeDetails = () => {
  selectedSearch.value = null
}

const startNewSearch = () => {
  selectedSearch.value = null
  form.reset()
}

const refreshList = () => {
  router.get('/certificates/by-document', {}, { preserveState: true })
}

const resetForm = () => {
  form.reset()
}

const markAllServices = () => {
  const allSelected = form.services.length === availableServices.length;
  if (allSelected) {
    form.services = [];
    form.region = '';
    form.certificateType = '';
  } else {
    form.services = availableServices.map(s => s.value);
    form.region = ''; 
    form.certificateType = ''; 
  }
}

const submitSearch = async () => {
    await form.post('/certificates/search', {
        onSuccess: (page) => {
            refreshList();

            const document = form.document;
            const searchResult = page.results.find(result => result.document === document);
            if (searchResult) {
                selectSearch(searchResult);
            }
        },
    });
}

const formatDocument = (document) => {
  if (!document) return ''
  const numbers = document.replace(/\D/g, '')
  if (numbers.length === 11) {
    return numbers.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  }
  return numbers.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getServicesText = (services) => {
  if (!services) return ''
  const serviceNames = {
    'federal-court': 'Tribunal Federal',
    'labour-court': 'Tribunal do Trabalho',
    'protests': 'Protestos',
    'receita-federal': 'Receita Federal',
    'debt-certificate': 'Certidão de Débitos',
    'cndt': 'CNDT'
  }
  
  const servicesArray = Array.isArray(services) 
    ? services 
    : JSON.parse(services || '[]')
    
  return servicesArray.map(s => serviceNames[s] || s).join(', ')
}

const getStatusClass = (status) => {
  const classes = {
    'completed': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'failed': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const copyToClipboard = (text) => {
  const input = document.createElement('input');
  input.value = text;
  document.body.appendChild(input);
  
  input.select();
  input.setSelectionRange(0, 99999);  
  try {
    const successful = document.execCommand('copy');
    const msg = successful ? 'Documento copiado para a área de transferência!' : 'Falha ao copiar o documento.';
    alert(msg);
  } catch (err) {
    console.error('Erro ao copiar o documento: ', err);
  }

  document.body.removeChild(input);
}

const showErrorModal = ref(false);
const normalErrors = ref([]);
const validatorErrors = ref([]);

// Adicionando um watch para exibir erros em um modal
watch(() => props.errors, (newErrors) => {
  if (Object.keys(newErrors).length > 0) {
    normalErrors.value = [];
    validatorErrors.value = [];
    
    // Classificando os erros
    for (const [key, value] of Object.entries(newErrors)) {
      if (Array.isArray(value)) {
        validatorErrors.value.push(...value); // Erros de validação
      } else {
        normalErrors.value.push(value); // Erros normais
      }
    }
    
    showErrorModal.value = true; // Exibe o modal
  }
});

const closeErrorModal = () => {
  showErrorModal.value = false; // Fecha o modal
}

</script>

<style scoped>
.fixed {
  position: fixed;
}
.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.z-50 {
  z-index: 50;
}
</style>
