<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">My Parcel</h1>
    <div class="max-w-2xl mx-auto">
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-6">Payment</h2>

        <!-- Payment Method Selection -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
          <el-select v-model="selectedPaymentMethod" class="w-full">
            <el-option label="Credit Card" value="CARD" />
            <el-option label="PayPal" value="PAYPAL" />
          </el-select>
        </div>

        <!-- Credit Card Form -->
        <div v-if="selectedPaymentMethod === 'CARD'" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Card Number</label>
            <el-input 
              v-model="cardForm.number"
              placeholder="Enter card number"
              maxlength="16"
              show-word-limit
              autocomplete="cc-number"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Cardholder Name</label>
            <el-input 
              v-model="cardForm.name"
              placeholder="Enter cardholder name"
              autocomplete="cc-name"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
              <el-input 
                v-model="cardForm.expiry"
                placeholder="MM/YY"
                maxlength="5"
                autocomplete="cc-exp"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Security Code</label>
              <el-input 
                v-model="cardForm.cvv"
                placeholder="CVV"
                maxlength="3"
                show-password
                autocomplete="cc-csc"
              />
            </div>
          </div>
        </div>

        <!-- PayPal Section -->
        <div v-else-if="selectedPaymentMethod === 'PAYPAL'" class="text-center p-8">
          <p class="mb-4">You will be redirected to PayPal to complete your payment.</p>
          <img src="/paypal-logo.png" alt="PayPal" class="mx-auto h-12" />
        </div>

        <!-- Total Amount -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
          <div class="flex justify-between items-center">
            <span class="text-lg font-medium">Total Amount:</span>
            <span class="text-xl font-bold">{{ orderData.paid_amount }}€</span>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
          <el-button @click="goBack">Previous Page</el-button>
          <el-button type="primary" @click="processPayment" :disabled="!isFormValid">
            Pay Now
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ElMessage } from 'element-plus'
import axios from 'axios'

export default {
  name: 'ParcelPaymentView',
  data() {
    return {
      orderData: {},
      selectedPaymentMethod: 'CARD',
      cardForm: {
        number: '',
        name: '',
        expiry: '',
        cvv: ''
      }
    }
  },
  computed: {
    isFormValid() {
      if (this.selectedPaymentMethod === 'CARD') {
        return this.cardForm.number.length === 16 &&
          this.cardForm.name.length > 0 &&
          this.cardForm.expiry.length === 5 &&
          this.cardForm.cvv.length === 3
      }
      return true
    }
  },
  created() {
    try {
      this.orderData = JSON.parse(this.$route.query.orderData || '{}')
    } catch (error) {
      console.error('Failed to parse order data:', error)
      ElMessage.error('Invalid order data')
      this.goBack()
    }
  },
  methods: {
    goBack() {
      this.$router.push({
        path: '/parcel/summary',
        query: {
          orderData: JSON.stringify(this.orderData)
        }
      })
    },
    async processPayment() {
      if (!this.isFormValid) {
        ElMessage.warning('Please fill in all required fields correctly')
        return
      }

      try {
        // Update payment method in order data
        this.orderData.payment_method = this.selectedPaymentMethod

        // In a real application, you would process the payment here
        // For now, we'll just submit the order
        const response = await axios.post('/api/order/orders', this.orderData)
        
        ElMessage({
          message: 'Payment successful! Order created.',
          type: 'success'
        })
        
        // Redirect to orders list
        this.$router.push('/orders')
      } catch (error) {
        ElMessage({
          message: error.response?.data?.error || 'Payment failed',
          type: 'error'
        })
      }
    }
  }
}
</script>

<style scoped>
.el-select {
  width: 100%;
}
</style> 