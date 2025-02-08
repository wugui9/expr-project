<template>
    <div class="p-6">
        <UserInfo />
        <h1 class="text-2xl font-bold mb-6">My Orders</h1>

        <div class="max-w-6xl mx-auto">
            <!-- Search and Filter -->
            <div class="mb-6 flex gap-4">
                <el-input v-model="searchQuery" placeholder="Search by order number" class="w-64" clearable>
                    <template #prefix>
                        <el-icon>
                            <Search />
                        </el-icon>
                    </template>
                </el-input>

                <el-select v-model="statusFilter" placeholder="Filter by status" clearable class="w-48">
                    <el-option label="All" value="" />
                    <el-option label="Express" value="EXPRESS" />
                    <el-option label="Standard" value="STANDARD" />
                </el-select>
            </div>

            <!-- Orders Table -->
            <el-table :data="filteredOrders" style="width: 100%"
                :empty-text="loading ? 'Loading...' : 'No orders found'" class="mb-6">
                <el-table-column prop="order_number" label="Order Number" min-width="180">
                    <template #default="{ row }">
                        <router-link :to="{ path: '/order-tracking', query: { order_id: row.id } }"
                            class="text-blue-600 hover:text-blue-800">
                            {{ row.order_number }}
                        </router-link>
                    </template>
                </el-table-column>

                <el-table-column prop="order_time" label="Order Time" min-width="180">
                    <template #default="{ row }">
                        {{ formatDate(row.order_time) }}
                    </template>
                </el-table-column>

                <el-table-column prop="paid_amount" label="Amount" min-width="120">
                    <template #default="{ row }">
                        €{{ row.paid_amount }}
                    </template>
                </el-table-column>

                <el-table-column prop="delivery_level" label="Delivery Type" min-width="150">
                    <template #default="{ row }">
                        <el-tag :type="row.delivery_level === 'EXPRESS' ? 'success' : 'info'">
                            {{ row.delivery_level }}
                        </el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="recipient_firstname" label="Recipient" min-width="180">
                    <template #default="{ row }">
                        {{ row.recipient_firstname }} {{ row.recipient_lastname }}
                    </template>
                </el-table-column>

                <el-table-column label="Actions" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button-group>
                            <el-button type="primary" size="small"
                                @click="$router.push({ path: '/order-tracking', query: { order_id: row.id } })">
                                Track
                            </el-button>
                        </el-button-group>
                    </template>
                </el-table-column>
            </el-table>

            <!-- Pagination -->
            <div class="flex justify-end">
                <el-pagination v-model:current-page="currentPage" v-model:page-size="pageSize" :total="totalOrders"
                    :page-sizes="[10, 20, 50, 100]" layout="total, sizes, prev, pager, next"
                    @size-change="handleSizeChange" @current-change="handleCurrentChange" />
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import axios from 'axios'
import { Search } from '@element-plus/icons-vue'
import UserInfo from '@/components/UserInfo.vue'

export default {
    name: 'OrdersView',
    components: {
        UserInfo,
        Search
    },
    setup() {
        const orders = ref([])
        const loading = ref(false)
        const searchQuery = ref('')
        const statusFilter = ref('')
        const currentPage = ref(1)
        const pageSize = ref(10)
        const totalOrders = ref(0)

        // Format date helper function
        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleString()
        }

        // Fetch orders from API
        const fetchOrders = async () => {
            loading.value = true
            try {
                const response = await axios.get('/api/order/list')
                orders.value = response.data
                totalOrders.value = orders.value.length
            } catch (error) {
                console.error('Failed to fetch orders:', error)
                ElMessage.error('Failed to load orders')
            } finally {
                loading.value = false
            }
        }

        // Filter and paginate orders
        const filteredOrders = computed(() => {
            let filtered = orders.value

            // Apply search filter
            if (searchQuery.value) {
                filtered = filtered.filter(order =>
                    order.order_number.toLowerCase().includes(searchQuery.value.toLowerCase())
                )
            }

            // Apply status filter
            if (statusFilter.value) {
                filtered = filtered.filter(order =>
                    order.delivery_level === statusFilter.value
                )
            }

            // Update total for pagination
            totalOrders.value = filtered.length

            // Apply pagination
            const start = (currentPage.value - 1) * pageSize.value
            const end = start + pageSize.value
            return filtered.slice(start, end)
        })

        // Pagination handlers
        const handleSizeChange = (val) => {
            pageSize.value = val
            currentPage.value = 1
        }

        const handleCurrentChange = (val) => {
            currentPage.value = val
        }

        onMounted(() => {
            fetchOrders()
        })

        return {
            orders,
            loading,
            searchQuery,
            statusFilter,
            currentPage,
            pageSize,
            totalOrders,
            filteredOrders,
            formatDate,
            handleSizeChange,
            handleCurrentChange
        }
    }
}
</script>