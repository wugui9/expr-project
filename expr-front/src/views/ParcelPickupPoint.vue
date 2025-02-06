<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Select Pickup Point</h1>

    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Column - Map -->
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="map-container relative bg-gray-100 rounded-lg overflow-hidden" style="height: 400px; width: 100%;">
            <div id="map" class="absolute inset-0 w-full h-full"></div>
            <div v-if="!isMapLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-100/80">
              <el-icon class="text-4xl animate-spin"><Loading /></el-icon>
            </div>
          </div>
        </div>

        <!-- Right Column - Point Selection -->
        <div class="space-y-6">
          <form id="pickup-point-form" @submit.prevent>
            <!-- City Selection -->
            <el-form-item label="City">
              <el-input 
                v-model="selectedCity" 
                placeholder="Enter city name"
                @input="debouncedSearch"
                name="city"
                autocomplete="shipping address-level2"
              />
            </el-form-item>

            <!-- Postal Code -->
            <el-form-item label="Postal Code">
              <el-input 
                v-model="postalCode" 
                placeholder="Enter postal code"
                @input="debouncedSearch"
                name="postal-code"
                autocomplete="shipping postal-code"
              />
            </el-form-item>

            <!-- Pickup Point Selection -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold">Available Pickup Points</h3>
              <div v-if="loading" class="text-gray-500">
                Loading pickup points...
              </div>
              <div v-else-if="error" class="text-red-500">
                {{ error }}
              </div>
              <div v-else-if="filteredStorages.length === 0" class="text-gray-500">
                No pickup points found for the current search
              </div>
              <el-radio-group 
                v-else
                v-model="selectedPointId" 
                class="flex flex-col space-y-4"
              >
                <el-radio 
                  v-for="point in filteredStorages" 
                  :key="point.id" 
                  :value="point.id"
                  class="w-full p-4 border rounded-lg"
                  @change="selectPoint(point)"
                >
                  <div class="flex flex-col">
                    <span class="font-semibold">Point {{ point.id }}</span>
                    <span class="text-sm text-gray-600">{{ point.detailed_address }}</span>
                    <span class="text-sm text-gray-600">{{ point.city }}, {{ point.postal_code }}</span>
                    <span class="text-sm text-gray-600">
                      Capacity: {{ point.capacity_volume_of_the_warehouse }}m³ / {{ point.capacity_weight_of_the_warehouse }}kg
                    </span>
                  </div>
                </el-radio>
              </el-radio-group>
            </div>
          </form>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="flex justify-between mt-8">
        <el-button @click="goBack">Previous Page</el-button>
        <el-button type="primary" @click="proceed" :disabled="!selectedPoint">
          Validate Selection
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ElMessage } from 'element-plus'
import { Loading } from '@element-plus/icons-vue'

export default {
  name: 'ParcelPickupPointView',
  components: {
    Loading
  },
  data() {
    return {
      selectedCity: '',
      postalCode: '',
      selectedPointId: null,
      selectedPoint: null,
      storageList: [],
      filteredStorages: [],
      loading: false,
      error: null,
      map: null,
      markers: [],
      isMapScriptLoaded: false,
      isMapLoaded: false,
      searchTimeout: null
    }
  },
  watch: {
    selectedCity() {
      this.debouncedSearch()
    },
    postalCode() {
      this.debouncedSearch()
    }
  },
  async created() {
    this.loading = true
    this.error = null
    try {
      console.log('Fetching storage data...')
      const response = await axios.get('/api/storage/list')
      console.log('API Response:', response.data)
      
      this.storageList = response.data.storages || []
      this.filteredStorages = [...this.storageList]
      
      if (this.storageList.length === 0) {
        this.error = 'No pickup points available'
      }

      // Load map after data is loaded
      this.loadGoogleMapsScript()
    } catch (error) {
      console.error('Failed to fetch storage list:', error)
      this.error = `Failed to load pickup points: ${error.message}`
    } finally {
      this.loading = false
    }
  },
  methods: {
    debouncedSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      this.searchTimeout = setTimeout(() => {
        this.handleSearch()
      }, 300)
    },

    handleSearch() {
      this.filteredStorages = this.storageList.filter(storage => {
        const cityMatch = !this.selectedCity || 
          storage.city.toLowerCase().includes(this.selectedCity.toLowerCase())
        const postalMatch = !this.postalCode || 
          storage.postal_code.includes(this.postalCode)
        return cityMatch && postalMatch
      })
      
      // Clear existing markers
      this.markers.forEach(marker => marker.setMap(null))
      this.markers = []
      
      // Add new markers
      this.addMarkers()
    },

    selectPoint(point) {
      this.selectedPoint = point
      this.selectedPointId = point.id

      // Center map on selected point
      if (this.map && point) {
        const lat = parseFloat(point.latitude)
        const lng = parseFloat(point.longitude)
        if (!isNaN(lat) && !isNaN(lng)) {
          this.map.setCenter({ lat, lng })
          this.map.setZoom(15)
        }
      }
    },

    addMarkers() {
      if (!this.map) return

      this.filteredStorages.forEach(storage => {
        const lat = parseFloat(storage.latitude)
        const lng = parseFloat(storage.longitude)
        
        if (!isNaN(lat) && !isNaN(lng)) {
          const position = { lat, lng }
          
          const marker = new google.maps.Marker({
            position,
            map: this.map,
            title: storage.detailed_address,
            animation: google.maps.Animation.DROP,
            label: {
              text: `${storage.id}`,
              color: '#FFFFFF',
              fontSize: '14px',
              fontWeight: 'bold'
            },
            icon: {
              path: google.maps.SymbolPath.CIRCLE,
              scale: 12,
              fillColor: '#4B5563',
              fillOpacity: 1,
              strokeColor: '#FFFFFF',
              strokeWeight: 2
            }
          })

          const infoWindow = new google.maps.InfoWindow({
            content: `
              <div class="p-4 max-w-xs">
                <h3 class="font-bold text-lg mb-2">Point ${storage.id}</h3>
                <p class="mb-1"><strong>City:</strong> ${storage.city}</p>
                <p class="mb-1"><strong>Address:</strong> ${storage.detailed_address}</p>
                <p class="mb-1"><strong>Postal Code:</strong> ${storage.postal_code}</p>
                <p class="mb-1"><strong>Volume Capacity:</strong> ${storage.capacity_volume_of_the_warehouse} m³</p>
                <p class="mb-1"><strong>Weight Capacity:</strong> ${storage.capacity_weight_of_the_warehouse} kg</p>
              </div>
            `
          })

          marker.addListener('click', () => {
            // Close all other info windows first
            this.markers.forEach(m => {
              if (m.infoWindow) {
                m.infoWindow.close()
              }
            })
            infoWindow.open(this.map, marker)
            this.selectPoint(storage)
          })

          marker.infoWindow = infoWindow
          this.markers.push(marker)
        }
      })

      if (this.markers.length > 0) {
        const bounds = new google.maps.LatLngBounds()
        this.markers.forEach(marker => bounds.extend(marker.getPosition()))
        this.map.fitBounds(bounds)
        this.map.setZoom(this.map.getZoom() - 1)
      }
    },

    loadGoogleMapsScript() {
      console.log('Loading Google Maps script...')
      if (!window.google && !this.isMapScriptLoaded) {
        const script = document.createElement('script')
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCseBl7rta84SCHJ2N9Knh6iFKFJIpK1Ns&libraries=places`
        script.async = true
        script.defer = true
        script.onload = () => {
          console.log('Google Maps script loaded')
          this.isMapScriptLoaded = true
          this.initMap()
        }
        script.onerror = (error) => {
          console.error('Failed to load Google Maps script:', error)
          ElMessage.error('Failed to load map')
        }
        document.head.appendChild(script)
      } else if (window.google) {
        console.log('Google Maps already loaded')
        this.initMap()
      }
    },

    async initMap() {
      console.log('Initializing map with data:', this.storageList)
      
      const mapElement = document.getElementById('map')
      if (!mapElement) {
        console.error('Map container not found')
        return
      }

      try {
        mapElement.style.display = 'none'
        mapElement.offsetHeight
        mapElement.style.display = ''

        const mapOptions = {
          zoom: 6,
          center: { lat: 46.603354, lng: 1.888334 }, // Center of France
          mapTypeControl: true,
          streetViewControl: true,
          fullscreenControl: true,
          styles: [
            {
              featureType: 'poi',
              elementType: 'labels',
              stylers: [{ visibility: 'off' }]
            }
          ]
        }

        this.map = new google.maps.Map(mapElement, mapOptions)
        this.isMapLoaded = true
        this.addMarkers()
      } catch (error) {
        console.error('Failed to initialize map:', error)
        this.error = 'Failed to load map'
      }
    },

    goBack() {
      this.$router.push('/parcel/recipient')
    },

    async proceed() {
      if (!this.selectedPoint) {
        ElMessage({
          message: 'Please select a pickup point',
          type: 'warning'
        })
        return
      }

      try {
        // Get order data from route query
        const orderData = JSON.parse(this.$route.query.orderData || '{}')
        
        // Add relay point to order data
        orderData.relay_point_id = this.selectedPoint.id
        orderData.shipping_address = this.selectedPoint.detailed_address // Set shipping address to pickup point address

        // Submit order
        const response = await axios.post('/api/order/orders', orderData)
        
        ElMessage({
          message: 'Order created successfully!',
          type: 'success'
        })

        // Redirect to orders list
        this.$router.push('/orders')
      } catch (error) {
        ElMessage({
          message: error.response?.data?.error || 'Failed to create order',
          type: 'error'
        })
      }
    }
  }
}
</script>

<style scoped>
.el-radio.is-bordered {
  margin-right: 0;
  margin-bottom: 10px;
}

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