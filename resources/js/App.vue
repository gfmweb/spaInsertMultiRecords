<script>
export default {
    name: "App",
    data(){
        return {
            all:0,
            updated:0,
            inserted:0
        }
    },
    methods:{
        getRecordsCount()
        {
            axios.get('/api/v1/getUsersCount').then(res=>{this.all = res.data})
        },
        ImportUsers()
        {
            axios.post('/api/v1/importUsers').then(res=>{
                this.all = res.data.all
                this.updated = res.data.updated
                this.inserted = res.data.inserted
            })
        }
    },
    mounted(){
        this.getRecordsCount()
    }

}
</script>

<template>
    <div class="container">
        <div class="my-5">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-lg btn-info" @click="ImportUsers">Импортировать пользователей</button>&nbsp;&nbsp;
                    Всего <strong>{{all.toLocaleString()}}</strong>&nbsp;
                    Добавлено <strong>{{inserted.toLocaleString()}}</strong>&nbsp;
                    Обновлено <strong>{{updated.toLocaleString()}}</strong>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>

</style>
