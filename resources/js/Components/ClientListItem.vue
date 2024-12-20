```vue
<template>
  <div 
    class="flex items-center px-4 py-3 border-b hover:bg-gray-50 cursor-pointer"
    :class="{ 'bg-gray-100': isSelected }"
    @click="$emit('select')"
  >
    <!-- Avatar -->
    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
      <span class="text-lg font-medium text-indigo-600">
        {{ getInitials(client.document) }}
      </span>
    </div>

    <!-- Info -->
    <div class="ml-4 flex-1">
      <div class="flex items-bottom justify-between">
        <h3 class="text-sm font-medium text-gray-900">
          {{ formatDocument(client.document) }}
        </h3>
        <span class="text-xs text-gray-500">
          {{ formatDate(client.created_at) }}
        </span>
      </div>
      <div class="flex items-center justify-between mt-1">
        <p class="text-sm text-gray-500 truncate">
          {{ getServicesText(client.services) }}
        </p>
        <span 
          class="px-2 py-1 text-xs rounded-full"
          :class="getStatusClass(client.status)"
        >
          {{ client.status }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select'])

const getInitials = (document) => {
  return document ? document.slice(0, 2) : ''
}

const formatDocument = (document) => {
  if (!document) return ''
  if (document.length === 11) {
    return document.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
  }
  return document.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5')
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
    'state-court': 'Tribunal de Justiça',
    'federal-court': 'Tribunal Federal',
    'labour-court': 'Tribunal do Trabalho',
    'protests': 'Protestos',
    'receita-federal': 'Receita Federal',
    'debt-certificate': 'Certidão de Débitos',
    'cndt': 'CNDT'
  }
  return Array.isArray(services) 
    ? services.map(s => serviceNames[s] || s).join(', ')
    : ''
}

const getStatusClass = (status) => {
  return {
    'completed': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'failed': 'bg-red-100 text-red-800'
  }[status] || 'bg-gray-100 text-gray-800'
}
</script>
```
