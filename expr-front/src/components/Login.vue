<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <el-card class="w-96">
      <template #header>
        <h1 class="text-2xl font-bold text-center">Express Management System</h1>
      </template>
      <el-form @submit.prevent="handleLogin">
        <el-form-item>
          <el-input 
            v-model="email" 
            placeholder="Email" 
            :disabled="loading"
          />
        </el-form-item>
        <el-form-item>
          <el-input 
            v-model="password" 
            type="password" 
            placeholder="Password" 
            :disabled="loading"
            @keyup.enter="handleLogin"
          />
        </el-form-item>
        <el-form-item>
          <el-button 
            type="primary" 
            @click="handleLogin" 
            :loading="loading"
            class="w-full"
          >
            Login
          </el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';

export default {
  setup() {
    const router = useRouter();
    const email = ref('');
    const password = ref('');
    const loading = ref(false);

    const handleLogin = async () => {
      if (!email.value || !password.value) {
        ElMessage.warning('Please enter email and password');
        return;
      }

      loading.value = true;
      try {
        const response = await fetch('/api/users/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: email.value,
            password: password.value,
          }),
        });

        const data = await response.json();

        if (!response.ok) {
          throw new Error(data.error || 'Login failed');
        }

        ElMessage.success('Login successful');
        router.push('/');
      } catch (error) {
        ElMessage.error(error.message || 'Login failed');
      } finally {
        loading.value = false;
      }
    };

    return {
      email,
      password,
      loading,
      handleLogin,
    };
  },
};
</script>

<style scoped>
.el-card {
  margin: 20px;
}
</style>
