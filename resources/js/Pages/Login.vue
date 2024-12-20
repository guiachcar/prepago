<template>
  <div class="flex items-center justify-center h-screen">
    <form @submit.prevent="login" class="bg-white p-6 rounded shadow-md w-80">
      <h2 class="text-xl mb-4">Login</h2>
      <div>
        <label for="email" class="block mb-1">Email</label>
        <input type="email" v-model="form.email" id="email" required class="border rounded w-full p-2" />
      </div>
      <div class="mt-4">
        <label for="password" class="block mb-1">Senha</label>
        <input type="password" v-model="form.password" id="password" required class="border rounded w-full p-2" />
      </div>
      <button type="submit" class="mt-4 bg-blue-500 text-white rounded py-2 w-full">Entrar</button>
      <p v-if="form.error" class="text-red-500 mt-2">{{ form.error }}</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  error: null,
});

const login = async () => {
  form.error = null;
  try {
    await form.post('/login');
  } catch (error) {
    form.error = 'Credenciais inválidas';
  }
};
</script>

<style scoped>
/* Adicione estilos conforme necessário */
</style>
