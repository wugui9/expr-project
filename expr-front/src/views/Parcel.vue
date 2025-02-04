<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">My Parcel</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Left Column - Main Form -->
      <div class="space-y-6">
        <!-- Parcel Weight -->
        <el-form-item label="Parcel Weight">
          <el-input-number 
            v-model="parcelForm.weight" 
            :min="1" 
            :precision="0"
            :step="1"
            placeholder="Enter parcel weight"
            class="w-full"
          />
          <span class="ml-2">kg</span>
        </el-form-item>

        <!-- Postal Code -->
        <el-form-item label="Postal Code">
          <el-input v-model="parcelForm.postalCode" placeholder="Enter postal code"></el-input>
        </el-form-item>

        <!-- Insurance Option -->
        <div class="space-y-2">
          <h3 class="text-lg font-semibold">Insurance Option</h3>
          <el-switch
            v-model="parcelForm.hasInsurance"
            active-text="Include Insurance"
            inactive-text="No Insurance"
          />
        </div>

        <!-- Parcel Compensation (only shown if insurance is selected) -->
        <div v-if="parcelForm.hasInsurance">
          <h3 class="text-lg font-semibold mb-2">Parcel Compensation</h3>
          <div class="flex items-center">
            <el-input-number 
              v-model="parcelForm.compensation" 
              :min="0" 
              :max="999" 
              class="w-24"
            ></el-input-number>
            <span class="ml-2">€</span>
          </div>
        </div>

        <!-- Delivery Method -->
        <div>
          <h3 class="text-lg font-semibold mb-2">Delivery Method</h3>
          <div class="space-y-4">
            <el-radio-group v-model="parcelForm.deliverySpeed">
              <div class="flex items-center space-x-2">
                <el-radio :label="'fast'">Fast (Delivery in 2j+2€)</el-radio>
              </div>
              <div class="flex items-center space-x-2">
                <el-radio :label="'normal'">Normal (Delivery in 7j, free)</el-radio>
              </div>
            </el-radio-group>
          </div>
        </div>

        <!-- Delivery Location -->
        <div>
          <h3 class="text-lg font-semibold mb-2">Delivery Location</h3>
          <div class="flex space-x-6">
            <el-radio-group v-model="parcelForm.deliveryLocation">
              <div class="space-y-4">
                <el-radio :label="'home'">
                  <div class="flex flex-col items-center">
                    <div class="w-24 h-24 border-2 border-gray-300 flex items-center justify-center mb-2">
                      <el-icon><House /></el-icon>
                    </div>
                    <span>Home Delivery</span>
                  </div>
                </el-radio>
                <el-radio :label="'pickup'">
                  <div class="flex flex-col items-center">
                    <div class="w-24 h-24 border-2 border-gray-300 flex items-center justify-center mb-2">
                      <el-icon><Location /></el-icon>
                    </div>
                    <span>Pickup Point</span>
                  </div>
                </el-radio>
              </div>
            </el-radio-group>
          </div>
        </div>
      </div>

      <!-- Right Column - Summary -->
      <div class="bg-gray-50 p-6 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Summary</h2>
        <div class="space-y-4">
          <div class="flex justify-between">
            <span>Receipt</span>
            <span>{{ parcelForm.receipt }}</span>
          </div>
          <div class="flex justify-between">
            <span>Weight</span>
            <span>{{ parcelForm.weight }} kg</span>
          </div>
          <div class="flex justify-between">
            <span>Destination</span>
            <span>{{ parcelForm.postalCode }}</span>
          </div>
          <div v-if="parcelForm.hasInsurance" class="flex justify-between">
            <span>Compensation</span>
            <span>{{ parcelForm.compensation }}€</span>
          </div>
          <div class="flex justify-between">
            <span>Delivery Time</span>
            <span>{{ deliveryTime }}</span>
          </div>
          <div v-if="parcelForm.hasInsurance" class="flex justify-between">
            <span>Insurance</span>
            <span>{{ insuranceAmount }}€</span>
          </div>
          <div class="flex justify-between font-bold">
            <span>Total Cost</span>
            <span>{{ totalCost }}€</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Next Page Button -->
    <div class="flex justify-center mt-8">
      <el-button 
        type="primary" 
        class="w-32" 
        @click="$router.push({
          path: '/parcel/recipient',
          query: { deliveryLocation: parcelForm.deliveryLocation }
        })"
      >
        Next Page
      </el-button>
    </div>
  </div>
</template>

<script>
import { House, Location } from '@element-plus/icons-vue'

export default {
  name: 'ParcelView',
  components: {
    House,
    Location
  },
  data() {
    return {
      parcelForm: {
        weight: 1,
        postalCode: '',
        hasInsurance: false,
        compensation: 25,
        deliverySpeed: 'normal',
        deliveryLocation: 'home',
        receipt: this.generateReceipt()
      }
    }
  },
  computed: {
    deliveryTime() {
      return this.parcelForm.deliverySpeed === 'fast' ? '2 days' : '7 days'
    },
    insuranceAmount() {
      return this.parcelForm.hasInsurance ? this.parcelForm.compensation * 0.01 : 0
    },
    totalCost() {
      let cost = 0
      // Add delivery cost
      if (this.parcelForm.deliverySpeed === 'fast') {
        cost += 2
      }
      // Add insurance cost only if insurance is selected
      if (this.parcelForm.hasInsurance) {
        cost += this.insuranceAmount
      }
      // Add base cost based on weight
      cost += this.parcelForm.weight * 1.5
      return cost.toFixed(2)
    }
  },
  methods: {
    generateReceipt() {
      return Math.random().toString(36).substring(2, 10).toUpperCase()
    }
  }
}
</script>

<style scoped>
.el-input-number {
  width: 120px;
}
</style> 