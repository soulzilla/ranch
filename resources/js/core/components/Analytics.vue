<template>
    <div class="mt-5 analytics-page">
        <template v-if="loading">
            <b-card>
                <b-skeleton height="40px" width="40%"></b-skeleton>
                <b-skeleton-table :rows="11" :columns="6"></b-skeleton-table>
                <div class="row">
                    <b-skeleton type="button" width="20%"></b-skeleton>
                    <b-skeleton type="button" width="20%" style="justify-content: end"></b-skeleton>
                </div>
            </b-card>
        </template>
        <template v-else>
            <b-card>
                <b-card-title>Степень Вашей маниакальности</b-card-title>

                <div class="row mb-3">
                    <div class="col-3">Промежуток дней</div>
                    <div class="col-3">
                        <b-input v-model="fromDay" placeholder="От"></b-input>
                    </div>
                    <div class="col-3">
                        <b-input v-model="toDay" placeholder="До"></b-input>
                    </div>
                    <div class="col-3">
                        <b-link @click="applyFilter" class="btn btn-primary" :disabled="!toDay && !fromDay">Поиск</b-link>
                    </div>
                </div>

                <b-table :items="history"></b-table>
                <div class="row">
                    <b-link :hidden="!prevPage" @click="loadPrev" class="btn btn-outline-primary">Предыдущая страница</b-link>
                    <b-link :hidden="!nextPage" @click="loadMore" class="btn btn-outline-primary ml-auto">Следующая страница</b-link>
                </div>
            </b-card>
        </template>
    </div>
</template>

<script>
export default {
    name: "Analytics",
    data: () => ({
        loading: true,
        nextPage: null,
        prevPage: null,
        fromDay: null,
        toDay: null,
        history: []
    }),
    methods: {
        loadMore: function () {
            let that = this

            axios.get(this.nextPage).then(function (response) {
                that.nextPage = response.data.links.next
                that.prevPage = response.data.links.prev
                that.history = response.data.data
            })
        },
        loadPrev: function () {
            let that = this

            axios.get(this.prevPage).then(function (response) {
                that.prevPage = response.data.links.prev
                that.nextPage = response.data.links.next
                that.history = response.data.data
            })
        },
        applyFilter: function () {
            let that = this

            axios.get('api/v1/history', {
                params: {
                    from: this.fromDay,
                    to: this.toDay
                }
            }).then(function (response) {
                that.prevPage = response.data.links.prev
                that.nextPage = response.data.links.next
                that.history = response.data.data
            })
        }
    },
    mounted() {
        let that = this

        axios.get('api/v1/history').then(function (response) {
            that.history = response.data.data
            that.nextPage = response.data.links.next
            that.loading = false
        })
    }
}
</script>

<style scoped>
.analytics-page {
    min-width: 70%;
}
</style>
