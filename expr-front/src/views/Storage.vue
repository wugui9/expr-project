<template>
  <div class="storage-page">
    <div class="storage-container">
      <h2 class="text-2xl font-bold mb-4">Point relais</h2>
      
      <div class="search-container mb-6">
        <div class="flex gap-4 items-center">
          <div class="flex-1">
            <label class="block mb-1">City</label>
            <el-input
              v-model="searchCity"
              placeholder="Enter city name"
              @input="debouncedSearch"
            />
          </div>
          <div class="flex-1">
            <label class="block mb-1">Postal Code</label>
            <el-input
              v-model="searchPostalCode"
              placeholder="Enter postal code"
              @input="debouncedSearch"
            />
          </div>
        </div>
      </div>

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

      <div class="map-wrapper relative bg-gray-100 rounded-lg overflow-hidden" style="height: 600px;">
        <div id="map" class="absolute inset-0"></div>
        <div v-if="!isMapLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-100/80">
          <el-icon class="text-4xl animate-spin"><Loading /></el-icon>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { ElMessage } from 'element-plus'
import { Loading } from '@element-plus/icons-vue'

export default {
  name: 'StorageView',
  components: {
    Loading
  },
  data() {
    return {
      storageList: [],
      filteredStorages: [],
      loading: false,
      error: null,
      map: null,
      markers: [],
      isMapScriptLoaded: false,
      isMapLoaded: false,
      searchCity: '',
      searchPostalCode: '',
      searchTimeout: null
    }
  },
  watch: {
    searchCity() {
      this.debouncedSearch()
    },
    searchPostalCode() {
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
        const cityMatch = !this.searchCity || 
          storage.city.toLowerCase().includes(this.searchCity.toLowerCase())
        const postalMatch = !this.searchPostalCode || 
          storage.postal_code.includes(this.searchPostalCode)
        return cityMatch && postalMatch
      })
      
      // Clear existing markers
      this.markers.forEach(marker => marker.setMap(null))
      this.markers = []
      
      // Add new markers
      this.addMarkers()
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
                <p class="mb-1"><strong>Ville:</strong> ${storage.city}</p>
                <p class="mb-1"><strong>Adresse:</strong> ${storage.detailed_address}</p>
                <p class="mb-1"><strong>Code Postal:</strong> ${storage.postal_code}</p>
                <p class="mb-1"><strong>Capacité Volume:</strong> ${storage.capacity_volume_of_the_warehouse} m³</p>
                <p class="mb-1"><strong>Capacité Poids:</strong> ${storage.capacity_weight_of_the_warehouse} kg</p>
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
          })

          marker.infoWindow = infoWindow // Store reference to info window
          this.markers.push(marker)
        }
      })

      if (this.markers.length > 0) {
        const bounds = new google.maps.LatLngBounds()
        this.markers.forEach(marker => bounds.extend(marker.getPosition()))
        this.map.fitBounds(bounds)
        
        // Add some padding to the bounds
        this.map.setZoom(this.map.getZoom() - 1)
      }
    },

    loadGoogleMapsScript() {
      console.log('Loading Google Maps script...')
      if (!window.google && !this.isMapScriptLoaded) {
        const script = document.createElement('script')
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCseBl7rta84SCHJ2N9Knh6iFKFJIpK1Ns&libraries=places,marker`
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
        // Force a reflow to ensure the map container has proper dimensions
        mapElement.style.display = 'none'
        mapElement.offsetHeight // force reflow
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

        // Wait for the map to be fully loaded
        google.maps.event.addListenerOnce(this.map, 'idle', () => {
          console.log('Map fully loaded')
          this.addMarkers()
          this.isMapLoaded = true
        })

      } catch (error) {
        console.error('Error initializing map:', error)
        this.error = `Failed to initialize map: ${error.message}`
      }
    }
  }
}
</script>

<style scoped>
.storage-page {
  width: 100%;
  min-height: 100vh;
}

.storage-container {
  width: 100%;
  margin: 0 auto;
}

.map-wrapper {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
}

#map {
  width: 100% !important;
  height: 100% !important;
  z-index: 1;
}

label {
  font-weight: 500;
}

.search-container {
  background-color: var(--el-bg-color);
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}
</style> 