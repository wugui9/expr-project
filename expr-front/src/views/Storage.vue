<template>
  <div class="storage-container p-6">
    <h2 class="text-2xl font-bold mb-4">Pickup Points Management</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <div class="map-container h-[500px] bg-gray-100 rounded-lg relative">
        <div id="map" class="w-full h-full rounded-lg"></div>
        <div v-if="!isMapLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-100">
          <el-icon class="text-4xl animate-spin"><Loading /></el-icon>
        </div>
      </div>

      <el-table
        v-loading="loading"
        :data="storageList"
        border
        style="width: 100%"
        height="500px"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="city" label="City" />
        <el-table-column prop="postal_code" label="Postal Code" width="120" />
        <el-table-column prop="detailed_address" label="Address" />
        <!-- <el-table-column prop="latitude" label="Latitude" width="120">
          <template #default="scope">
            {{ scope.row.latitude || 'N/A' }}
          </template>
        </el-table-column>
        <el-table-column prop="longitude" label="Longitude" width="120">
          <template #default="scope">
            {{ scope.row.longitude || 'N/A' }}
          </template>
        </el-table-column>
        <el-table-column prop="capacity_volume_of_the_warehouse" label="Volume" width="120">
          <template #default="scope">
            {{ scope.row.capacity_volume_of_the_warehouse }} m³
          </template>
        </el-table-column>
        <el-table-column prop="capacity_weight_of_the_warehouse" label="Weight" width="120">
          <template #default="scope">
            {{ scope.row.capacity_weight_of_the_warehouse }} kg
          </template>
        </el-table-column> -->
      </el-table>
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
      loading: false,
      map: null,
      markers: [],
      isMapScriptLoaded: false,
      isMapLoaded: false
    }
  },
  async created() {
    this.loading = true
    try {
      const response = await axios.get('/api/storage/list')
      this.storageList = response.data.storages || []
      
      if (this.storageList.length === 0) {
        ElMessage.warning('No pickup points found')
      }

      // Load map after data is loaded
      this.loadGoogleMapsScript()
    } catch (error) {
      console.error('Failed to fetch storage list:', error)
      ElMessage.error('Failed to load pickup points')
    } finally {
      this.loading = false
    }
  },
  methods: {
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
      
      console.log('Map container dimensions:', {
        width: mapElement.clientWidth,
        height: mapElement.clientHeight,
        offsetWidth: mapElement.offsetWidth,
        offsetHeight: mapElement.offsetHeight
      })

      try {
        // Initialize the map
        this.map = new google.maps.Map(mapElement, {
          zoom: 6,
          center: { lat: 46.603354, lng: 1.888334 }, // Center of France
          mapTypeControl: true,
          streetViewControl: true,
          fullscreenControl: true
        })

        // Add markers for each storage location
        for (const storage of this.storageList) {
          console.log('Processing storage:', storage)
          const lat = parseFloat(storage.latitude)
          const lng = parseFloat(storage.longitude)
          
          if (!isNaN(lat) && !isNaN(lng)) {
            console.log('Creating marker at:', lat, lng)
            
            const position = { lat, lng }
            
            // Create the marker using regular Marker
            const marker = new google.maps.Marker({
              position,
              map: this.map,
              title: storage.detailed_address,
              animation: google.maps.Animation.DROP
            })

            // Add info window
            const infoWindow = new google.maps.InfoWindow({
              content: `
                <div class="p-2">
                  <h3 class="font-bold">${storage.city}</h3>
                  <p>${storage.detailed_address}</p>
                  <p>Postal Code: ${storage.postal_code}</p>
                  <p>Volume: ${storage.capacity_volume_of_the_warehouse} m³</p>
                  <p>Weight: ${storage.capacity_weight_of_the_warehouse} kg</p>
                </div>
              `
            })

            // Add click listener
            marker.addListener('click', () => {
              infoWindow.open(this.map, marker)
            })

            this.markers.push(marker)
          }
        }

        // Fit bounds to show all markers
        if (this.markers.length > 0) {
          const bounds = new google.maps.LatLngBounds()
          this.markers.forEach(marker => bounds.extend(marker.getPosition()))
          this.map.fitBounds(bounds)
        } else {
          console.log('No valid markers to display')
        }

        this.isMapLoaded = true
      } catch (error) {
        console.error('Error initializing map:', error)
        ElMessage.error('Failed to initialize map')
      }
    }
  }
}
</script>

<style scoped>
.storage-container {
  max-width: 1400px;
  margin: 0 auto;
}

.map-container {
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  min-height: 500px;
  background: #f5f5f5;
}

#map {
  width: 100% !important;
  height: 100% !important;
  min-height: 500px;
}
</style> 