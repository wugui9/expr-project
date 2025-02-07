<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Delivery Information</h1>

    <div class="max-w-2xl mx-auto">
      <form id="recipient-form" @submit.prevent>
        <el-form :model="recipientForm" label-position="top">
          <h2 class="text-xl font-semibold mb-4">Recipient Information</h2>
          
          <!-- Last Name -->
          <el-form-item label="Last Name" required>
            <el-input 
              v-model="recipientForm.lastName" 
              placeholder="Enter last name"
              name="shipping-lastname"
              autocomplete="shipping family-name" 
            />
          </el-form-item>

          <!-- First Name -->
          <el-form-item label="First Name" required>
            <el-input 
              v-model="recipientForm.firstName" 
              placeholder="Enter first name"
              name="shipping-firstname"
              autocomplete="shipping given-name"
            />
          </el-form-item>

          <!-- Phone -->
          <el-form-item label="Phone Number" required>
            <el-input 
              v-model="recipientForm.phone" 
              placeholder="Enter phone number"
              name="shipping-phone"
              autocomplete="shipping tel"
              type="tel"
            />
          </el-form-item>

          <!-- Email -->
          <el-form-item label="Email" required>
            <el-input 
              v-model="recipientForm.email" 
              placeholder="Enter email address"
              name="shipping-email"
              autocomplete="shipping email"
              type="email"
            />
          </el-form-item>

          <!-- Delivery Address -->
          <el-form-item label="Delivery Address" required>
            <el-input 
              v-model="recipientForm.address" 
              placeholder="Enter delivery address"
              name="shipping-address"
              autocomplete="shipping street-address"
            />
          </el-form-item>

          <!-- Additional Address Information -->
          <el-form-item label="Additional Address Information">
            <el-input 
              v-model="recipientForm.addressComplement" 
              placeholder="Apartment, suite, floor, etc."
              name="shipping-address-2"
              autocomplete="shipping address-line2"
            />
          </el-form-item>

          <!-- Language Preference -->
          <el-form-item label="Preferred Language">
            <el-select 
              v-model="recipientForm.language" 
              class="w-full"
              name="language"
              autocomplete="language"
            >
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
      </form>
    </div>
  </div>
</template>

<script>
import { ElMessage } from 'element-plus'
import axios from 'axios'

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
          !this.recipientForm.phone || !this.recipientForm.email || 
          !this.recipientForm.address) {
        ElMessage({
          message: 'Please fill in all required fields',
          type: 'warning'
        })
        return
      }

      try {
        // Get parcel data from route query
        const parcelData = JSON.parse(this.$route.query.parcelData || '{}')
        
        // Prepare order data
        const orderData = {
          ...parcelData,
          recipient_lastname: this.recipientForm.lastName,
          recipient_firstname: this.recipientForm.firstName,
          recipient_phone: this.recipientForm.phone,
          delivery_address: this.recipientForm.address,
          delivery_address_complement: this.recipientForm.addressComplement,
          shipping_address: this.isPickupDelivery ? '' : this.recipientForm.address, // Will be set to pickup point address later if isPickupDelivery
          payment_method: 'CARD'
        }

        if (this.isPickupDelivery) {
          // If pickup delivery, go to pickup point selection
          await this.$router.push({
            path: '/parcel/pickup-point',
            query: {
              orderData: JSON.stringify(orderData)
            }
          })
        } else {
          // If home delivery, go to summary page
          await this.$router.push({
            path: '/parcel/summary',
            query: {
              orderData: JSON.stringify(orderData)
            }
          })
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