<style>
    .widget-user-header{
        background-position: center center;
        background-size: cover;
        height: 250px !important;
    }
    .widget-user .card-footer{
        padding: 0;
    }

</style>

<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header text-white" style="background-image:url('./img/photo1.png')">
                        <h3 class="widget-user-username" v-text="form.name"></h3>
                        <h5 class="widget-user-desc" v-text="form.type"></h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>

            <!-- tab -->

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Activity</a></li>
                            <li class="nav-item"><a class="nav-link active show" href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Activity Tab -->
                            <div class="tab-pane" id="activity">
                                <h3 class="text-center">Display User Activity</h3>
                            </div>
                            <!-- Setting Tab -->
                            <div class="tab-pane active show" id="settings">
                                <form class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Name</label>

                                        <div class="col-sm-12">
                                            <input v-model="form.name" type="name" class="form-control" id="inputName" placeholder="Name"
                                                   :class="{ 'is-invalid': form.errors.has('name') }">
                                            <has-error :form="form" field="name"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  for="inputEmail" class="col-sm-2 control-label">Email</label>

                                        <div class="col-sm-12">
                                            <input v-model="form.email" type="email" class="form-control" id="inputEmail" placeholder="Email"
                                                   :class="{ 'is-invalid': form.errors.has('email') }">
                                            <has-error :form="form" field="email"></has-error>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputbio" class="col-sm-2 control-label">Bio</label>

                                        <div class="col-sm-12">
                                            <textarea v-model="form.bio" class="form-control" id="inputbio" placeholder="Bio"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="photo" class="col-sm-2 control-label">Profile Photo</label>
                                        <div class="col-sm-12">
                                            <input type="file" @change="updateProfile" name="photo" class="form-input" id="photo">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label  for="password" class="col-sm-12 control-label">Password (leave empty if not changing)</label>

                                        <div class="col-sm-12">
                                            <input  v-model="form.password" type="password" class="form-control" id="password" placeholder="Password"
                                                    :class="{ 'is-invalid': form.errors.has('password') }">
                                            <has-error :form="form" field="password"></has-error>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-12">
                                            <button @click.prevent="updateinfo" type="submit" class="btn btn-success">Update</button>
                                            <button value="Reload Page" onClick="reload" class="btn btn-dark">Cancel</button>

                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- end tabs -->
        </div>
    </div>
</template>



<script>
    export default {
        data(){
            return {
                form : new Form ({
                    id: '',
                    name : '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''

                })
            }
        },

        methods:{
            updateProfile(e){
                // console.log('uploading');
                let file= e.target.files[0];
                // console.log(file);
                let reader = new FileReader();
                if(file['size'] < 2111775){
                    reader.onload =  (file) =>{
                        this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'You are uploading a large file. Please upload a smaller file',
                    })
                }

            },

            updateinfo(){
                this.$Progress.start();
                this.form.put('api/profile/')
                    .then(()=>{
                        //Fire Event
                        Fire.$emit('LoadUser');
                        //Success Message
                        Swal.fire({
                            type: 'success',
                            title: 'Done',
                        })
                        this.$Progress.finish()

                    })
                    .catch(()=>{
                        this.$Progress.fail()
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong !!',
                        })
                    })
            },

            getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/" +this.form.photo;
                return photo;
            },

            loadUser(){
                axios.get("api/profile").then(({data}) => (this.form.fill(data)));
            }
        },
        created() {
            this.loadUser();
            Fire.$on('LoadUser',()=> {
                this.loadUser();
            })
        }
    }
</script>
