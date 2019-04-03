<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdmin()">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User table Table</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Add New <i class="fas fa-user-plus fa-fw"></i> </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody><tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered At</th>
                                <th>Modify</th>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{user.id}}</td>
                                <td>{{user.name | upText}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.type | upText}}</td>
                                <td>{{user.created_at | filterDate}}</td>

                                <td>
                                    <a href="#" @click="editModal(user)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    /
                                    <a href="#" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </td>
                            </tr>

                            </tbody></table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-show="!editmode" id="addNew">Add New User</h5>
                        <h5 class="modal-title" v-show="editmode" id="addNew">Update User</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editmode ? updateUser() : createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name" placeholder="Name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.email" type="email" name="email" placeholder="Email"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>
                            <div class="form-group">
                            <textarea v-model="form.bio" name="bio" placeholder="Bio for User"
                                      class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }">
                            <has-error :form="form" field="bio"></has-error>
                            </textarea>
                            </div>
                            <div class="form-group">
                                <select v-model="form.type" type="text" name="type" placeholder="Name"
                                        class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                    <option value="">Select User Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                            <div class="form-group">
                                <input v-model="form.password" type="password" name="password" placeholder="Password"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                            <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return  {

                editmode: false,

                users : {},

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

        methods: {
            editModal(user){
                this.editmode = true;
                this.form.clear();
               this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(user);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            loadUsers(){
               if (this.$gate.isAdmin())
               {
                   axios.get("api/user").then(({data}) => (this.users = data.data));
               }
            },

            deleteUser(id){
                this.$Progress.start();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    //send  request to the server
                    if(result.value){
                        this.form.delete('api/user/'+id).then(()=>{
                            {
                                //Fire Event
                                Fire.$emit('LoadUser');

                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )
                            }
                            this.$Progress.finish();
                        }) .catch(()=>{
                            toast.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong! Unable to Delete User'
                            })
                            this.$Progress.fail();
                        })
                    }
                })
            },

            createUser(){
                //Progress bar
                this.$Progress.start();

                //Post User API
                this.form.post('api/user')
                    .then(()=>{
                            //Fire Event
                            Fire.$emit('LoadUser');

                            //hide Modal
                            $('#addNew').modal('hide');

                            //Sweet Alert
                            toast.fire({
                                type: 'success',
                                title: 'User Created Successfully'
                            });
                        }
                    )
                    .catch(()=>{


                        toast.fire({
                            type: 'error',
                            title: 'Oops...',
                            html: 'Something went wrong! </br> Unable to create New User. '
                        })
                        this.$Progress.fail();

                    });
                this.$Progress.finish();
            },

            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/' +this.form.id)
                    .then(()=>{
                        //Sweet Alert
                        toast.fire({
                            type: 'success',
                            title: 'User Updated Successfully'
                        });

                        //Fire Event
                        Fire.$emit('LoadUser');

                        //hide Modal
                        $('#addNew').modal('hide');

                        this.$Progress.finish();

                    })
                    .catch(()=>{
                        this.$Progress.fail();
                        $('#addNew').modal('hide');
                        toast.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: 'Unable to Update User!'
                        })
                    });
            },
        },

        created() {
            this.loadUsers();
            Fire.$on('LoadUser',()=> {
                this.loadUsers();
            })
        }
    }
</script>
