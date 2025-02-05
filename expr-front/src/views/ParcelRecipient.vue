<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Delivery Information</h1>

    <div class="max-w-2xl mx-auto">
      <el-form :model="recipientForm" label-position="top">
        <h2 class="text-xl font-semibold mb-4">Recipient Information</h2>
        
        <!-- Last Name -->
        <el-form-item label="Last Name" required>
          <el-input v-model="recipientForm.lastName" placeholder="Enter last name" />
        </el-form-item>

        <!-- First Name -->
        <el-form-item label="First Name" required>
          <el-input v-model="recipientForm.firstName" placeholder="Enter first name" />
        </el-form-item>

        <!-- Phone -->
        <el-form-item label="Phone Number" required>
          <el-input v-model="recipientForm.phone" placeholder="Enter phone number" />
        </el-form-item>

        <!-- Email -->
        <el-form-item label="Email" required>
          <el-input v-model="recipientForm.email" placeholder="Enter email address" />
        </el-form-item>

        <!-- Delivery Address -->
        <el-form-item label="Delivery Address" required>
          <el-input v-model="recipientForm.address" placeholder="Enter delivery address" />
        </el-form-item>

        <!-- Additional Address Information -->
        <el-form-item label="Additional Address Information">
          <el-input v-model="recipientForm.addressComplement" placeholder="Apartment, suite, floor, etc." />
        </el-form-item>

        <!-- Language Preference -->
        <el-form-item label="Preferred Language">
          <el-select v-model="recipientForm.language" class="w-full">
            <el-option label="English" value="en" />
            <el-option label="French" value="fr" />
          </el-select>
        </el-form-item>

        <!-- Save to Address Book -->
        <div class="mt-4">
          <el-checkbox v-model="recipientForm.saveToAddressBook">
            Save to my address book
          </el-checkbox>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
          <el-button @click="goBack">Previous Page</el-button>
          <el-button type="primary" @click="validateAndProceed">
            {{ isPickupDelivery ? 'Next Page' : 'Validate My Shipment' }}
          </el-button>
        </div>
      </el-form>
    </div>
  </div>
</template>

<script>
import { ElMessage } from 'element-plus'

export default {
  name: 'ParcelRecipientView',
  data() {
    return {
      recipientForm: {
        lastName: '',
        firstName: '',
        phone: '',
        email: '',
        address: '',
        addressComplement: '',
        language: 'en',
        saveToAddressBook: false
      }
    }
  },
  computed: {
    isPickupDelivery() {
      return this.$route.query.deliveryLocation === 'pickup'
    }
  },
  methods: {
    goBack() {
      this.$router.push('/parcel')
    },
    async validateAndProceed() {
      // Basic form validation
      if (!this.recipientForm.lastName || !this.recipientForm.firstName || 
          !this.recipientForm.phone || !this.recipientForm.email) {
        ElMessage({
          message: 'Please fill in all required fields',
          type: 'warning'
        })
        return
      }

      try {
        if (this.isPickupDelivery) {
          // If pickup delivery, go to pickup point selection
          await this.$router.push({
            path: '/parcel/pickup-point',
            query: {
              ...this.$route.query,
              recipient: JSON.stringify(this.recipientForm)
            }
          })
        } else {
          // If home delivery, show success message and submit the form
          ElMessage({
            message: 'Shipment validated successfully!',
            type: 'success'
          })
          // Here you would typically submit the form data to your backend
          console.log('Form data:', this.recipientForm)
        }
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