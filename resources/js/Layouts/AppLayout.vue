```vue
<template>
  <div class="h-screen flex">
    <!-- Sidebar -->
    <div class="w-96 bg-white border-r flex flex-col">
      <!-- Header -->
      <div class="h-16 bg-gray-100 flex items-center justify-between px-4 border-b">
        <div class="p-4 border-b">
          <img src="https://b3183664.smushcdn.com/3183664/wp-content/uploads/2023/05/logo-nova.png?lossy=1&strip=1&webp=1" alt="Logo" class="h-8 mb-4" />
        </div>
        <button
          @click="logout"
          class="bg-red-500 text-white rounded-md w-8 h-8 flex items-center justify-center hover:bg-red-600"
          title="Sair"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3m0 0l4-4m-4 4l4 4m12-12v5a2 2 0 002 2h3" />
          </svg>
        </button>
      </div>

      <!-- Search -->
      <div class="p-3 border-b">
        <div class="relative">
          <input 
            v-model="searchQuery"
            type="search" 
            placeholder="Pesquisar consulta..."
            class="w-full py-2 px-4 pl-10 bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="handleSearch"
          >
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- Clients List -->
      <div class="flex-1 overflow-y-auto">
        <slot name="sidebar" />
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col bg-gray-50">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const searchQuery = ref('')

const emit = defineEmits(['search'])

const handleSearch = () => {
  emit('search', searchQuery.value)
}

// Observa mudanças no searchQuery e emite o evento após um pequeno delay
watch(searchQuery, (newValue) => {
  emit('search', newValue)
})

const form = useForm({});
const logout = async () => {
  await form.post('/logout');
};
</script>
```
