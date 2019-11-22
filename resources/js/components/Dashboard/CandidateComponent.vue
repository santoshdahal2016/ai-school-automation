<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="container">


        <material-card
            color="green"
            title="Upload Csv"
        >
                <input accept=".csv" label="Select File..." @change="onFileChange($event)"  type="file" :v-model="file">
        </material-card>
        <!--Table View Started-->
        <material-card
            color="green"
            title="Candidates"
        >
            <v-select
                v-model="options.filter"
                :items="[{'text':'None','value':'none'},{'text':'Paid','value':'paid'},{'text':'Unpaid','value':'unpaid'}]"
                label="Select Filter"
            ></v-select>
            <v-data-table
                :items="data"
                :headers="headers"
                :loading="loading" loading-text="Loading... Please wait"
                :options.sync="options"
                :server-items-length="totalData">

                <template v-slot:top>
                    <v-text-field @change="fetch" v-model="options.search" prepend-icon="search" label="Search ......"
                                  class="mx-4">
                    </v-text-field>
                </template>

                <template v-slot:item.Scholarship="{ item }">
                    <v-chip :color="item.Scholarship=='Yes'?'teal':'blue'" dark>
                        {{item.Scholarship }}
                    </v-chip>
                </template>

                <template v-slot:item.status="{ item }">
                    <v-icon small class="mr-2" v-if="item.status === 'email_send'" color="green darken-2">
                        mdi-check
                    </v-icon>
                    <v-icon small class="mr-2" v-if="item.status === 'email_unsend'" color="red darken-2">
                        mdi-block-helper
                    </v-icon>
                </template>

                <template v-slot:item.pdf="{ item }">
                    <v-icon small class="mr-2" @click="downloadReceipt(item)" color="green darken-2">
                        mdi-receipt
                    </v-icon>
                    <v-icon small @click="downloadConfirmation(item)" color="green darken-2">
                        mdi-account-check
                    </v-icon>

                    <v-icon v-if="item.status === 'email_unsend'" small @click="sendEmail(item)" color="green darken-2">
                        mdi-send
                    </v-icon>
                </template>

                <template slot="no-data">
                    <v-alert :value="true" color="error" icon="warning">
                        Sorry, No Candidate Yet.
                    </v-alert>
                </template>

            </v-data-table>
        </material-card>
    </div>
</template>

<script>

    import {mapGetters} from 'vuex';

    export default {
        /*injecting the vee-validate*/
        inject: ['$validator'],

        data() {
            return {

                /*Data Table Related Variable*/
                loading: true,
                headers: [
                    {text: 'Name', align: 'left', sortable: false, value: 'Name'},
                    {text: 'Email', value: 'Email_Address'},
                    {text: 'Scholarship', value: 'Scholarship'},
                    {text: 'amount', value: 'amount', sortable: false},
                    {text: 'currency', value: 'currency', sortable: false},
                    {text: 'Pdf', value: 'pdf', sortable: false},
                    {text: 'Status', value: 'status', sortable: false},

                ],
                data: [],
                totalData: 0,

                /*Csv Upload Variable*/
                file: null,

                /*Search and Query Related Variable*/
                options: {
                    page: 1,
                    itemsPerPage: 10,
                    search: undefined,
                    sort: 'desc',
                    filter: 'all'
                },


                path: JSON.parse(JSON.stringify(this.$route.path))
            }
        }, computed: {
            ...mapGetters(['serverUrl']),
            getFullPath() {
                return this.$route.path
            }
        },
        watch: {
            options: {
                handler() {
                    this.fetch();
                },
                deep: true,
            },
            getFullPath() {
                if (this.getFullPath === this.path) {
                    this.fetch()
                }
            }

        },

        methods: {
            /*Fetch Users*/
            fetch() {
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('fetchCandidates', this.options)
                        .then(response => {
                            this.data = response.data.data;
                            this.loading = false;
                            this.totalData = response.data.meta.total;
                        }).catch(error => {
                        // console.log(error.response.status);
                    });
                })

            },

            /*Download Receipt*/
            downloadReceipt(item) {
                window.open(this.serverUrl + '/api/candidates/receipt/' + item.id, "_blank");

            },


            /*Download Confirmation*/
            downloadConfirmation(item) {
                window.open(this.serverUrl + '/api/candidates/confirmation/' + item.id, "_blank");
            },

            /*Send  Confirmation and Receipt*/
            sendEmail(item) {

                this.loading = true;
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('sendEmail', item).then(response => {
                        if (response.status === 200) {
                            this.$store.dispatch('showSuccessSnackbar', 'Email have been Send Successfully');
                            this.fetch();
                            this.loading = false;
                        }
                    }).catch(error => {

                        this.loading = false;

                    })
                })
            },


            /*Csv file onchange event trigger*/
            onFileChange(event) {
                this.file = event.target.files[0];

                var formData = new FormData();
                if (this.file) {
                    formData.append('import_file', this.file);
                }

                this.loading = true;
                return new Promise((resolve, reject) => {
                    this.$store.dispatch('csvLoad', formData).then(response => {
                        if (response.status === 201) {
                            this.$store.dispatch('showSuccessSnackbar', 'Csv Uploaded Successfully');
                            this.fetch();
                            this.loading = false;
                        }
                    }).catch(error => {

                        this.loading = false;

                    })
                })
            },
        },
    }
</script>
