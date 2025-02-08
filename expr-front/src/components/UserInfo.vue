<template>
  <div class="absolute top-4 right-4 flex items-center gap-4">
    <span v-if="userInfo" class="text-gray-700">
      Welcome, {{ userInfo.firstname }} {{ userInfo.lastname }}
    </span>
    <el-button type="danger" size="small" @click="handleLogout" :loading="loading">Logout</el-button>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'

export default {
  name: 'UserInfo',
  setup() {
    const router = useRouter()
    const userInfo = ref(null)
    const loading = ref(false)

    const getCurrentUser = async () => {
      try {
        const response = await fetch('/api/users/current')
        const data = await response.json()
        
        if (response.ok) {
          userInfo.value = data
        } else {
          throw new Error(data.error || 'Failed to get user info')
        }
      } catch (error) {
        ElMessage.error(error.message)
        router.push('/login')
      }
    }

    const handleLogout = async () => {
      loading.value = true
      try {
        const response = await fetch('/api/users/logout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          }
        })

        const data = await response.json()
        
        if (response.ok) {
          ElMessage.success(data.message || 'Logged out successfully')
          userInfo.value = null
          await new Promise(resolve => setTimeout(resolve, 500))
          await router.push('/login')
        } else {
          throw new Error(data.error || 'Logout failed')
        }
      } catch (error) {
        ElMessage.error(error.message)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      getCurrentUser()
    })

    return {
      userInfo,
      loading,
      handleLogout
    }
  }
}
</script> 