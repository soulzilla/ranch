<template>
    <div class="mt-5 page-index">
        <template v-if="loading">
            <div class="my-3 text-right">
                <b-skeleton width="20%" height="20px" class="ml-auto"></b-skeleton>
            </div>

            <b-card class="mw-100">
                <b-skeleton height="40px" width="40%"></b-skeleton>

                <div class="row">
                    <template v-for="i in 4">
                        <div class="col-6 yard">
                            <b-skeleton height="200px"></b-skeleton>
                        </div>
                    </template>
                </div>

                <div class="row">
                    <b-skeleton type="button"></b-skeleton>
                    <b-skeleton type="button" class="ml-auto"></b-skeleton>

                    <div class="col-12 px-0">
                        <b-skeleton type="input" class="mt-3"></b-skeleton>
                    </div>

                    <div class="col-12 px-0">
                        <b-skeleton type="input" class="mt-3"></b-skeleton>
                    </div>
                </div>
            </b-card>
        </template>
        <template v-else>
            <div class="my-3 text-right">
                День #{{ day }}
            </div>

            <b-card>
                <b-card-title>
                    Ферма для овечек
                </b-card-title>

                <div class="row">
                    <template v-for="yard of yards">
                        <div class="col-6 yard">
                            Загон #{{yard.id}}
                            <div class="overflow-auto">
                                <b-list-group>
                                    <template v-for="item of yard.sheep">
                                        <b-list-group-item>
                                            Овечка #{{item.id}}
                                        </b-list-group-item>
                                    </template>
                                </b-list-group>
                            </div>
                        </div>
                    </template>
                </div>

                <hr/>

                <div class="row">
                    <b-link class="btn btn-outline-primary" @click="refresh" v-b-tooltip="'Обновить страницу'">Обновить</b-link>
                    <b-link class="btn btn-outline-danger ml-auto"
                            @click="killRandomSheep" v-b-tooltip="'Унести случайную овечку на убой'">
                        Зарубить овечек
                    </b-link>

                    <div class="col-12 px-0">
                        <b-input-group class="mt-3" prepend="$">
                            <b-form-input placeholder="Введите текст команды" v-model="commandText"></b-form-input>
                            <b-input-group-append>
                                <b-button variant="outline-success" @click="executeCommand" :disabled="!commandText.length">
                                    Выполнить
                                </b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </div>

                    <div class="col-12 px-0 mt-3">
                        <router-link to="/analytics" class="btn btn-block btn-outline-secondary">
                            Степень Вашей маниакальности (статистика)
                        </router-link>
                    </div>
                </div>
            </b-card>
        </template>
    </div>
</template>

<script>
export default {
    name: "Index",
    data: () => ({
        loading: true,
        commandText: '',
        day: 1,
        yards: []
    }),
    methods: {
        refresh: function () {
            this.loading = true
            this.loadData()
        },
        killRandomSheep: function () {
            let that = this

            axios.delete('api/v1/sheep', {
                params: {
                    random: 1
                }
            }).then(function (response) {
                that.loading = true
                that.loadData()
                that.$bvToast.toast('Овечка #' + response.data.id + ' была убита :(', {
                    variant: 'danger'
                })
            })
        },
        executeCommand: function () {
            let availableCommands = ['add', 'kill', 'move'],
                that = this,
                commandName = this.commandText.split(' ')[0]

            if (!availableCommands.includes(commandName)) {
                this.$bvToast.toast('Неверная команда. Попробуйте одну из этих: add [номер_загона], kill [номер_овечки], move [номер_овечки] [номер_загона]', {
                    variant: 'danger'
                })
            } else {
                switch (commandName) {
                    case 'add':
                        let yard_id = this.commandText.split(' ')[1];
                        if (!yard_id) {
                            this.$bvToast.toast('Отсутствует обязательный параметр [номер_загона]', {
                                variant: 'danger'
                            })
                        } else {
                            axios.post('api/v1/sheep', {
                                yard_id: yard_id
                            }).then(function () {
                                that.loading = true
                                that.loadData()
                                that.$bvToast.toast('Овечка добавлена в загон #' + yard_id, {
                                    variant: 'success'
                                })
                            }).catch(function () {
                                that.$bvToast.toast('Что-то пошло не так. Попробуйте ещё раз.', {
                                    variant: 'warning'
                                })
                            })
                        }
                        break;
                    case 'kill':
                        let sheep_id = this.commandText.split(' ')[1];
                        if (!sheep_id) {
                            this.$bvToast.toast('Отсутствует обязательный параметр [номер_овечки]', {
                                variant: 'danger'
                            })
                        } else {
                            axios.delete('api/v1/sheep', {
                                params: {
                                    id: sheep_id
                                }
                            }).then(function () {
                                that.loading = true
                                that.loadData()
                                that.$bvToast.toast('Овечка #' + sheep_id + ' отправлена на убой :(', {
                                    variant: 'info'
                                })
                            }).catch(function () {
                                that.$bvToast.toast('Что-то пошло не так. Попробуйте ещё раз.', {
                                    variant: 'warning'
                                })
                            })
                        }
                        break;
                    case 'move':
                        let id = this.commandText.split(' ')[1],
                            yard = this.commandText.split(' ')[2]
                        if (!id) {
                            this.$bvToast.toast('Отсутствует обязательный параметр [номер_овечки]', {
                                variant: 'danger'
                            })
                        } else if (!yard) {
                            this.$bvToast.toast('Отсутствует обязательный параметр [номер_загона]', {
                                variant: 'danger'
                            })
                        } else {
                            axios.put('api/v1/sheep', {
                                yard_id: yard
                            }, {
                                params: {
                                    id: id
                                }
                            }).then(function () {
                                that.loading = true
                                that.loadData()
                                that.$bvToast.toast('Овечка #' + id + ' перемещена в загон #' + yard, {
                                    variant: 'success'
                                })
                            }).catch(function () {
                                that.$bvToast.toast('Что-то пошло не так. Попробуйте ещё раз.', {
                                    variant: 'warning'
                                })
                            })
                        }
                        break;
                }
            }
        },
        loadData: function () {
            let that = this

            axios.get('api/v1/yards', {
                params: {
                    expand: 'sheep'
                }
            }).then(function (response) {
                that.yards = response.data.data

                axios.get('api/v1/history', {
                    params: {
                        limit: 1,
                        sortBy: 'id,desc'
                    }
                }).then(function (response) {
                    that.day = response.data.data[0].id

                    that.loading = false
                })
            })
        }
    },
    mounted() {
        this.loadData()
    }
}
</script>

<style scoped>
.page-index {
    min-width: 50%;
}

.yard {
    margin-bottom: 1rem;
    max-height: 200px;
    overflow-y: scroll;
}
</style>
