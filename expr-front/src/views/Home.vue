<template>
  <div>
    <!-- User Info Section -->
    <UserInfo />
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-center mb-12">Our Services</h1>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- My Package Card -->
        <el-card class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('parcel')">
          <div class="text-center">
            <el-icon class="text-4xl mb-4">
              <Box />
            </el-icon>
            <h2 class="text-xl font-semibold mb-2">Send Package</h2>
            <p class="text-gray-600">Send your package to your destination</p>
          </div>
        </el-card>

        <!-- Pickup Points Card -->
        <el-card class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('pickup')">
          <div class="text-center">
            <el-icon class="text-4xl mb-4">
              <Location />
            </el-icon>
            <h2 class="text-xl font-semibold mb-2">Pickup Points</h2>
            <p class="text-gray-600">Find nearest pickup locations</p>
          </div>
        </el-card>

        <!-- Order Tracking Card -->
        <el-card class="hover:shadow-xl transition-shadow cursor-pointer h-64 flex flex-col items-center justify-center"
          @click="handleCardClick('tracking')">
          <div class="text-center">
            <el-icon class="text-4xl mb-4">
              <Timer />
            </el-icon>
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
import { useRouter } from 'vue-router'
import UserInfo from '@/components/UserInfo.vue'

export default {
  name: 'Home',
  components: {
    Box,
    Location,
    Timer,
    UserInfo
  },
  setup() {
    const router = useRouter()
  },
  methods: {

    handleCardClick(type) {
      switch (type) {
        case 'parcel':
          this.$router.push('/parcel')
          break
        case 'pickup':
          this.$router.push('/storage')
          break
        case 'tracking':
          this.$router.push('/orders')
          break
      }
    }
  }
}
</script>