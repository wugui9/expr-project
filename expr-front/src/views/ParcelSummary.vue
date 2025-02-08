<template>
  <div>
    <UserInfo />
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-6">My Parcel</h1>
      <div class="max-w-2xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow">
          <h2 class="text-xl font-semibold mb-6">Order Summary</h2>

          <!-- Weight -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Weight</label>
            <div class="mt-1 p-2 bg-gray-50 rounded">
              {{ orderData.weight }} kg
            </div>
          </div>

          <!-- Delivery Address -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Delivery Address</label>
            <div class="mt-1 p-2 bg-gray-50 rounded">
              {{ orderData.shipping_address }}
            </div>
          </div>

          <!-- Delivery Cost -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Delivery Cost</label>
            <div class="mt-1 p-2 bg-gray-50 rounded">
              {{ deliveryCost }}€
            </div>
          </div>

          <!-- Insurance -->
          <div class="mb-4" v-if="orderData.compensation">
            <label class="block text-sm font-medium text-gray-700">Insurance</label>
            <div class="mt-1 p-2 bg-gray-50 rounded">
              <el-checkbox v-model="hasInsurance" disabled>Insurance</el-checkbox>
              <div v-if="hasInsurance" class="mt-2">
                Compensation Amount: {{ orderData.compensation }}€
              </div>
            </div>
          </div>

          <!-- Total Cost -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Total Cost</label>
            <div class="mt-1 p-2 bg-gray-50 font-bold rounded">
              {{ orderData.paid_amount }}€
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="flex justify-between mt-8">
            <el-button @click="goBack">Previous Page</el-button>
            <el-button type="primary" @click="proceedToPayment">
              Proceed to Payment
            </el-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ElMessage } from 'element-plus'
import axios from 'axios'
import UserInfo from '@/components/UserInfo.vue'

export default {
  name: 'ParcelSummaryView',
  components: {
    UserInfo
  },
  data() {
    return {
      orderData: {},
      hasInsurance: false
    }
  },
  computed: {
    deliveryCost() {
      return this.orderData.delivery_level === 'EXPRESS' ? 2 : 0
    }
  },
  created() {
    try {
      this.orderData = JSON.parse(this.$route.query.orderData || '{}')
      this.hasInsurance = !!this.orderData.compensation
    } catch (error) {
      console.error('Failed to parse order data:', error)
      ElMessage.error('Invalid order data')
      this.goBack()
    }
  },
  methods: {
    goBack() {
      if (this.orderData.relay_point_id) {
        this.$router.push('/parcel/pickup-point')
      } else {
        this.$router.push('/parcel/recipient')
      }
    },
    async proceedToPayment() {
      try {
        await this.$router.push({
          path: '/parcel/payment',
          query: {
            orderData: JSON.stringify(this.orderData)
          }
        })
      } catch (error) {
        console.error('Navigation error:', error)
        ElMessage({
          message: 'Navigation failed. Please try again.',
          type: 'error'
        })
      }
    }
  }
}
</script> 