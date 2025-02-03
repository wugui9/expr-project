<template>
  <div class="login">
    <el-card class="box-card">
      <h1>Login</h1>
      <el-form @submit.prevent="login">
        <el-form-item>
          <el-input v-model="username" placeholder="Username" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="password" type="password" placeholder="Password" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="login">Login</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const username = ref('');
const password = ref('');

const login = () => {
  // Handle login logic here
  console.log('Logging in with', username.value, password.value);
};
</script>

<style scoped>
.login {
  max-width: 400px;
  margin: 0 auto;
  padding: 1rem;
}
</style>
