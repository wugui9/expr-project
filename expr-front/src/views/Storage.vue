<template>
  <div class="storage-container p-6">
    <h2 class="text-2xl font-bold mb-4">Pickup Points Management</h2>
    
    <el-table
      v-loading="loading"
      :data="storageList"
      border
      style="width: 100%"
    >
      <el-table-column prop="id" label="ID" width="80" />
      <el-table-column prop="city" label="City" />
      <el-table-column prop="postal_code" label="Postal Code" width="120" />
      <el-table-column prop="detailed_address" label="Address" />
      <el-table-column prop="capacity_volume_of_the_warehouse" label="Volume Capacity" width="150">
        <template #default="scope">
          {{ scope.row.capacity_volume_of_the_warehouse }} m³
        </template>
      </el-table-column>
      <el-table-column prop="capacity_weight_of_the_warehouse" label="Weight Capacity" width="150">
        <template #default="scope">
          {{ scope.row.capacity_weight_of_the_warehouse }} kg
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import axios from 'axios'
import { ElMessage } from 'element-plus'

export default {
  name: 'StorageView',
  data() {
    return {
      storageList: [],
      loading: false
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
    } catch (error) {
      console.error('Failed to fetch storage list:', error)
      ElMessage.error('Failed to load pickup points')
    } finally {
      this.loading = false
    }
  }
}
</script>

<style scoped>
.storage-container {
  max-width: 1200px;
  margin: 0 auto;
}
</style> 