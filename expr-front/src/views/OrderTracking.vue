<template>
    <div class="p-6">
        <UserInfo />
        <h1 class="text-2xl font-bold mb-6">Order Tracking</h1>

        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column - Map -->
                <div class="lg:w-1/3 bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold mb-4">Delivery Location</h2>
                    <div class="map-container relative bg-gray-100 rounded-lg overflow-hidden" style="height: 600px;">
                        <div id="map" class="absolute inset-0 w-full h-full"></div>
                        <div v-if="!isMapLoaded"
                            class="absolute inset-0 flex items-center justify-center bg-gray-100/80">
                            <el-icon class="text-4xl animate-spin">
                                <Loading />
                            </el-icon>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Details -->
                <div class="lg:w-2/3 bg-white p-6 rounded-lg shadow">
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
                                <span class="font-medium">Payment Amount:</span> €{{ orderDetails.paid_amount || '0.00'
                                }}
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <span class="font-medium">Payment Method:</span> {{ orderDetails.payment_method || 'N/A'
                                }}
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Progress -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold mb-4">Delivery Progress</h2>
                        <div class="relative">
                            <div class="flex items-center justify-between">
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
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ElMessage } from 'element-plus'
import axios from 'axios'
import { Box, Van, Location, Loading } from '@element-plus/icons-vue'
import UserInfo from '@/components/UserInfo.vue'

export default {
    name: 'OrderTrackingView',
    components: {
        UserInfo,
        Box,
        Van,
        Location,
        Loading
    },
    setup() {
        const route = useRoute()
        const orderId = ref(route.query.order_id)
        const currentStep = ref(1)
        const currentStatus = ref('Processing')
        const orderDetails = ref({})
        const isMapLoaded = ref(false)
        const map = ref(null)
        const marker = ref(null)

        // Format date helper function
        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleString()
        }

        const loadGoogleMapsScript = () => {
            if (!window.google) {
                const script = document.createElement('script')
                script.src = `https://maps.googleapis.com/maps/api/js?key=`
                script.async = true
                script.defer = true
                script.onload = () => {
                    initMap()
                }
                document.head.appendChild(script)
            } else {
                initMap()
            }
        }

        const initMap = () => {
            const mapElement = document.getElementById('map')
            if (!mapElement) return

            map.value = new google.maps.Map(mapElement, {
                zoom: 12,
                center: { lat: 46.603354, lng: 1.888334 }, // Default center (France)
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]
            })

            isMapLoaded.value = true
            updateMarker()
        }

        const updateMarker = async () => {
            if (!map.value || !orderDetails.value.shipping_address) return

            try {
                // Use Geocoding service to convert address to coordinates
                const geocoder = new google.maps.Geocoder()
                const address = orderDetails.value.shipping_address

                geocoder.geocode({ address }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const position = results[0].geometry.location

                        // Clear existing marker
                        if (marker.value) {
                            marker.value.setMap(null)
                        }

                        // Create new marker
                        marker.value = new google.maps.Marker({
                            position,
                            map: map.value,
                            title: address,
                            animation: google.maps.Animation.DROP,
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 12,
                                fillColor: '#10B981',
                                fillOpacity: 1,
                                strokeColor: '#FFFFFF',
                                strokeWeight: 2
                            }
                        })

                        // Create info window
                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                                <div class="p-4">
                                    <h3 class="font-bold mb-2">Delivery Location</h3>
                                    <p>${address}</p>
                                </div>
                            `
                        })

                        marker.value.addListener('click', () => {
                            infoWindow.open(map.value, marker.value)
                        })

                        // Center map on marker
                        map.value.setCenter(position)
                    }
                })
            } catch (error) {
                console.error('Geocoding failed:', error)
            }
        }

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

                // Update map marker after getting order details
                if (isMapLoaded.value) {
                    updateMarker()
                }

            } catch (error) {
                console.error('Failed to fetch order status:', error)
                ElMessage.error('Failed to load order status')
            }
        }

        onMounted(() => {
            if (orderId.value) {
                fetchOrderStatus()
                loadGoogleMapsScript()
            } else {
                ElMessage.warning('No order ID provided')
            }
        })

        return {
            orderId,
            currentStep,
            currentStatus,
            orderDetails,
            formatDate,
            isMapLoaded
        }
    }
}
</script>

<style scoped>
.map-container {
    position: relative;
    min-height: 400px;
}

#map {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
}
</style>