<template>
  <div>
    <!-- User Info Section -->
    <div class="absolute top-4 right-4 flex items-center gap-4">
      <span v-if="userInfo" class="text-gray-700">
        Welcome, {{ userInfo.firstname }} {{ userInfo.lastname }}
      </span>
      <el-button type="danger" size="small" @click="handleLogout">Logout</el-button>
    </div>

    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-12">Our Services</h1>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- My Package Card -->
        <el-card 
          class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('parcel')"
        >
          <div class="text-center">
            <el-icon class="text-4xl mb-4"><Box /></el-icon>
            <h2 class="text-xl font-semibold mb-2">Send Package</h2>
            <p class="text-gray-600">Send your package to your destination</p>
          </div>
        </el-card>

        <!-- Pickup Points Card -->
        <el-card 
          class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('pickup')"
        >
          <div class="text-center">
            <el-icon class="text-4xl mb-4"><Location /></el-icon>
            <h2 class="text-xl font-semibold mb-2">Pickup Points</h2>
            <p class="text-gray-600">Find nearest pickup locations</p>
          </div>
        </el-card>

        <!-- Order Tracking Card -->
        <el-card 
          class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('tracking')"
        >
          <div class="text-center">
            <el-icon class="text-4xl mb-4"><Timer /></el-icon>
            <h2 class="text-xl font-semibold mb-2">Order Tracking</h2>
            <p class="text-gray-600">Track your order status</p>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script>
import { Box, Location, Timer } from '@element-plus/icons-vue'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'

export default {
  name: 'Home',
  components: {
    Box,
    Location,
    Timer
  },
  setup() {
    const router = useRouter()
    const userInfo = ref(null)

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

    const handleLogout = () => {
      // Clear the JWT cookie by setting it to expire
      document.cookie = 'jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
      router.push('/login')
      ElMessage.success('Logged out successfully')
    }

    onMounted(() => {
      getCurrentUser()
    })

    return {
      userInfo,
      handleLogout
    }
  },
  methods: {
    handleCardClick(type) {
      switch(type) {
        case 'parcel':
          this.$router.push('/parcel')
          break
        case 'pickup':
          this.$router.push('/storage')
          break
        case 'tracking':
          this.$router.push('/order-tracking')
          break
      }
    }
  }
}
</script> 