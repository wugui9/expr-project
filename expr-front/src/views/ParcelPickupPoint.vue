<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Select Pickup Point</h1>

    <div class="max-w-6xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Column - Map -->
        <div class="bg-gray-100 rounded-lg p-4">
          <div class="map-wrapper relative bg-gray-100 rounded-lg overflow-hidden" style="height: 600px;">
            <div id="map" class="absolute inset-0"></div>
            <div v-if="!isMapLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-100/80">
              <el-icon class="text-4xl animate-spin"><Loading /></el-icon>
            </div>
          </div>
        </div>

        <!-- Right Column - Point Selection -->
        <div class="space-y-6">
          <!-- Search Container -->
          <div class="search-container">
            <div class="flex gap-4 items-center">
              <div class="flex-1">
                <label class="block mb-1">City</label>
                <el-input
                  v-model="selectedCity"
                  placeholder="Enter city name"
                  @input="debouncedSearch"
                />
              </div>
              <div class="flex-1">
                <label class="block mb-1">Postal Code</label>
                <el-input
                  v-model="postalCode"
                  placeholder="Enter postal code"
                  @input="debouncedSearch"
                />
              </div>
            </div>
          </div>

          <!-- Status Messages -->
          <div v-if="loading" class="mb-4">
            Loading data...
          </div>
          <div v-if="error" class="mb-4 text-red-500">
            {{ error }}
          </div>
          <div v-if="filteredStorages.length === 0" class="mb-4">
            No pickup points found for the current search
          </div>
          <div v-else class="mb-4">
            Found {{ filteredStorages.length }} pickup points
          </div>

          <!-- Pickup Point Selection -->
          <div class="space-y-4">
            <h3 class="text-lg font-semibold">Available Pickup Points</h3>
            <el-radio-group 
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

  <!-- Info Window Template -->
  <template id="info-window-template">
    <div style="padding: 1rem; max-width: 20rem;">
      <h3 style="font-weight: bold; margin-bottom: 0.5rem;"></h3>
      <p class="city" style="margin-bottom: 0.25rem;"></p>
      <p class="address" style="margin-bottom: 0.25rem;"></p>
      <p class="postal" style="margin-bottom: 0.25rem;"></p>
      <p class="volume" style="margin-bottom: 0.25rem;"></p>
      <p class="weight" style="margin-bottom: 0.25rem;"></p>
    </div>
  </template>
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

          // Trigger click on the corresponding marker
          const marker = this.markers.find(m => m.storage.id === point.id)
          if (marker) {
            google.maps.event.trigger(marker, 'click')
          }
        }
      }
    },

    createInfoWindow(storage) {
      const template = document.getElementById('info-window-template');
      const content = template.content.cloneNode(true);
      
      content.querySelector('h3').textContent = 'Point ' + storage.id;
      content.querySelector('.city').textContent = 'City: ' + storage.city;
      content.querySelector('.address').textContent = 'Address: ' + storage.detailed_address;
      content.querySelector('.postal').textContent = 'Postal Code: ' + storage.postal_code;
      content.querySelector('.volume').textContent = 'Volume: ' + storage.capacity_volume_of_the_warehouse + ' m3';
      content.querySelector('.weight').textContent = 'Weight: ' + storage.capacity_weight_of_the_warehouse + ' kg';

      return content;
    },

    addMarkers() {
      if (!this.map) return

      this.filteredStorages.forEach(storage => {
        const lat = parseFloat(storage.latitude)
        const lng = parseFloat(storage.longitude)
        
        if (!isNaN(lat) && !isNaN(lng)) {
          const position = { lat, lng }

          // Create marker content
          const markerContent = document.createElement('div')
          markerContent.className = 'marker-content'
          markerContent.style.backgroundColor = '#4B5563'
          markerContent.style.color = '#FFFFFF'
          markerContent.style.padding = '4px 8px'
          markerContent.style.borderRadius = '50%'
          markerContent.style.border = '2px solid #FFFFFF'
          markerContent.style.fontSize = '14px'
          markerContent.style.fontWeight = 'bold'
          markerContent.style.minWidth = '24px'
          markerContent.style.height = '24px'
          markerContent.style.display = 'flex'
          markerContent.style.alignItems = 'center'
          markerContent.style.justifyContent = 'center'
          markerContent.textContent = storage.id
          
          const marker = new google.maps.marker.AdvancedMarkerElement({
            map: this.map,
            position,
            title: storage.detailed_address,
            content: markerContent
          })

          const infoWindow = new google.maps.InfoWindow({
            content: "Point " + storage.id + " - " + storage.detailed_address
          })

          marker.addListener('click', () => {
            // Close all other info windows first
            this.markers.forEach(m => {
              if (m.infoWindow) {
                m.infoWindow.close()
              }
            })
            infoWindow.open({
              anchor: marker,
              map: this.map
            })
            // Select the point in the list
            this.selectPoint(storage)
          })

          marker.infoWindow = infoWindow
          marker.storage = storage
          this.markers.push(marker)
        }
      })

      if (this.markers.length > 0) {
        const bounds = new google.maps.LatLngBounds()
        this.markers.forEach(marker => bounds.extend(marker.position))
        this.map.fitBounds(bounds)
        this.map.setZoom(this.map.getZoom() - 1)
      }
    },

    loadGoogleMapsScript() {
      console.log('Loading Google Maps script...')
      if (!window.google && !this.isMapScriptLoaded) {
        const script = document.createElement('script')
        script.src = \`https://maps.googleapis.com/maps/api/js?key=AIzaSyCseBl7rta84SCHJ2N9Knh6iFKFJIpK1Ns&libraries=places,marker\`
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

    proceed() {
      if (this.selectedPoint) {
        ElMessage({
          message: 'Pickup point selected successfully!',
          type: 'success'
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

.map-wrapper {
  border-radius: 0.5rem;
  overflow: hidden;
}

.marker-content {
  cursor: pointer;
  transition: transform 0.2s;
}

.marker-content:hover {
  transform: scale(1.1);
}
</style> 