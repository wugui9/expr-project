<template>
    <div class="p-6">
        <UserInfo />
        <h1 class="text-2xl font-bold mb-6">Order Tracking</h1>

        <div class="max-w-4xl mx-auto">
            <div class="bg-white p-6 rounded-lg shadow">
                <!-- Order Details -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Order Details</h2>
                    <div class="space-y-2">
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Order Number:</span> {{ orderDetails.order_number || 'N/A' }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Order Time:</span> {{ orderDetails.order_time || 'N/A' }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Payment Amount:</span> €{{ orderDetails.paid_amount || '0.00' }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Payment Method:</span> {{ orderDetails.payment_method || 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Tracking Map -->
                <div class="mb-8">
                    <div class="relative h-48 bg-gray-100 rounded-lg overflow-hidden">
                        <div class="absolute inset-0">
                            <!-- Tracking Progress Bar -->
                            <div class="flex items-center justify-between px-8 h-full">
                                <!-- Step 1: In Stock -->
                                <div class="relative flex flex-col items-center">
                                    <div :class="['w-12 h-12 rounded-full flex items-center justify-center',
                                        currentStep >= 1 ? 'bg-green-500 text-white' : 'bg-gray-300']">
                                        <el-icon>
                                            <Box />
                                        </el-icon>
                                    </div>
                                    <span class="mt-2 text-sm">In Stock</span>
                                </div>

                                <!-- Connection Line 1 -->
                                <div :class="['flex-1 h-1', currentStep >= 2 ? 'bg-green-500' : 'bg-gray-300']"></div>

                                <!-- Step 2: In Delivery -->
                                <div class="relative flex flex-col items-center">
                                    <div :class="['w-12 h-12 rounded-full flex items-center justify-center',
                                        currentStep >= 2 ? 'bg-green-500 text-white' : 'bg-gray-300']">
                                        <el-icon>
                                            <Van />
                                        </el-icon>
                                    </div>
                                    <span class="mt-2 text-sm">In Delivery</span>
                                </div>

                                <!-- Connection Line 2 -->
                                <div :class="['flex-1 h-1', currentStep >= 3 ? 'bg-green-500' : 'bg-gray-300']"></div>

                                <!-- Step 3: At Pickup Point -->
                                <div class="relative flex flex-col items-center">
                                    <div :class="['w-12 h-12 rounded-full flex items-center justify-center',
                                        currentStep >= 3 ? 'bg-green-500 text-white' : 'bg-gray-300']">
                                        <el-icon>
                                            <Location />
                                        </el-icon>
                                    </div>
                                    <span class="mt-2 text-sm">At Pickup Point</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipient Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Recipient Information</h2>
                    <div class="space-y-2">
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Name:</span>
                            {{ orderDetails.recipient_firstname }} {{ orderDetails.recipient_lastname }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Phone:</span> {{ orderDetails.recipient_phone }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Delivery Address:</span> {{ orderDetails.delivery_address }}
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Shipping Address:</span> {{ orderDetails.shipping_address }}
                        </div>
                    </div>
                </div>

                <!-- Package Information -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Package Information</h2>
                    <div class="space-y-2">
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Weight:</span> {{ orderDetails.weight }}kg
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="font-medium">Delivery Level:</span> {{ orderDetails.delivery_level }}
                        </div>
                    </div>
                </div>

                <!-- Navigation Button -->
                <div class="flex justify-center mt-8">
                    <el-button @click="$router.push('/orders')">Back to Orders</el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import axios from 'axios'
import { Box, Van, Location } from '@element-plus/icons-vue'
import UserInfo from '@/components/UserInfo.vue'

export default {
    name: 'OrderTrackingView',
    components: {
        UserInfo,
        Box,
        Van,
        Location
    },
    setup() {
        const route = useRoute()
        const orderId = ref(route.query.order_id)
        const currentStep = ref(1)
        const currentStatus = ref('Processing')
        const orderDetails = ref({})

        const fetchOrderStatus = async () => {
            try {
                const response = await axios.get(`/api/order?id=${orderId.value}`)
                orderDetails.value = response.data

                // Update tracking information based on delivery level
                switch (orderDetails.value.delivery_level) {
                    case 'EXPRESS':
                        currentStep.value = 3
                        currentStatus.value = 'Express Delivery - Priority Processing'
                        break
                    case 'STANDARD':
                        currentStep.value = 2
                        currentStatus.value = 'Standard Delivery - In Progress'
                        break
                    default:
                        currentStep.value = 1
                        currentStatus.value = 'Processing'
                }

            } catch (error) {
                console.error('Failed to fetch order status:', error)
                ElMessage.error('Failed to load order status')
            }
        }

        onMounted(() => {
            if (orderId.value) {
                fetchOrderStatus()
            } else {
                ElMessage.warning('No order ID provided')
            }
        })

        return {
            orderId,
            currentStep,
            currentStatus,
            orderDetails
        }
    }
}
</script>