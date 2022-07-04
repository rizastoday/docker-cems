<?= $this->extend('layouts/layout') ?>
<?= $this->section('content') ?>
    <div id="email" v-cloak>
        <!-- <loading :show="control.loading"></loading> -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-3 p-3">
                    <div class="card-body table-responsive">
                        <button class="btn btn-primary text-capitalize my-3" @click="OpenModal">
                            <i class="fa fa-plus"></i>
                            <span>Add User</span>
                        </button>
                        <h4>User List</h4>
                        <table class="table table-sm table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>name</th>
                                    <th>e-mail</th>
                                    <th>group</th>
                                    <th>password updated</th>
                                    <th>deactivate</th>
                                    <th>option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in users">
                                    <td v-text="item.name ?? '-'"></td>
                                    <td v-text="item.email ?? '-'"></td>
                                    <td v-text="item.groupName ?? '-'"></td>
                                    <td v-text="item.password_updated_at ?? '-'"></td>
                                    <td v-text="item.deleted_at ?? '-'"></td>
                                    <td class="d-flex flex-row justify-content-around">
                                        <button class="btn btn-sm btn-info" @click="OpenModal(item)">
                                            Edit
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="option-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Options
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="option-dropdown">
                                                <span class="dropdown-item" @click="ChangePassword(item.userId)">Change Password</span>
                                                <span class="dropdown-item" v-if="item.deleted_at" @click="DeleteUser(item.userId, false, 'restore')">Restore</span>
                                                <span class="dropdown-item" v-if="!item.deleted_at" @click="DeleteUser(item.userId, false, 'delete')">Deactivate</span>
                                                <span class="dropdown-item" v-if="!item.deleted_at" @click="DeleteUser(item.userId, true, 'delete')">Permanently Delete</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            New User
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-sync">
                        <div class="p-3">
                            <div class="card">
                                <form class="card-body" id="formUser" autocomplete="off">
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">Name</label>
                                        <div class="col-md-12">
                                            <input class="form-control" v-model="form.name" name="name" type="text" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="form-group column">
                                        <label class="col-md-12 col-form-label">E-Mail</label>
                                        <div class="col-md-12">
                                            <input class="form-control" :disabled="form.userId" v-model="form.email" name="email" type="text" placeholder="E-Mail">
                                        </div>
                                    </div>
                                    <div class="form-group column" v-if=!form.userId>
                                        <label class="col-md-12 col-form-label">Password</label>
                                        <div class="col-md-12">
                                            <input class="form-control"  v-model="form.password" name="password" type="password" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="form-group column" v-if="groups.length && !form.userId">
                                        <label class="col-md-12 col-form-label">Group</label>
                                        <div class="col-md-12">
                                            <select class="form-control" v-model="form.groupId">
                                                <option v-for="item in groups" v-text="item.name" :value="item.groupId"></option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success float-right" @click="SaveUser">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script>
        let v = new Vue({
            el: '#email',
            data:()=>({
                table: null,
                modal: null,
                users: [],
                groups: [],
                form: {
                    userId: null,
                    name: null,
                    email: null,
                    password: null,
                    groupId: null
                }
            }),
            mounted(){
                this.modal = new coreui.Modal(document.getElementById('modalUser'), {
                    backdrop: 'static',
                    show: false
                })
                this.GetUser()
                this.GetGroup()
            },
            methods:{
                async GetUser(){
                    await fetch('<?= base_url('setting/get-user') ?>', {
                        method: 'GET'
                    }).then(res => res.json()).then(data => {
                        if(data.status){
                            this.users.splice(0)
                            this.users.push(...data.data.filter(x => x.userType != 'admin' && x.userId != '<?= session()->get('userId') ?>').map(x => {
                                return {
                                    userId: x.userId,
                                    name: x.name,
                                    email: x.email,
                                    groupName: x.groupName,
                                    password_updated_at: x.password_updated_at,
                                    deleted_at: x.deleted_at,
                                }
                            }))
                        }else {
                            throw data.message ?? 'Server was busy or connection is unstable'
                        }
                    }).catch(er => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to load page',
                            text: er,
                            allowOutsideClick: false,
                        }).then(res => {
                            if(er == 'Unauthenticated' || er == 'Expired token'){
                                location.href = '<?= '/' ?>auth/logout'
                            }
                        })
                    })
                },
                async GetGroup(){
                    await fetch('<?= base_url('setting/get-group') ?>', {
                        method: 'GET'
                    }).then(res => res.json()).then(data => {
                        if(data.status){
                            this.groups.splice(0)
                            this.groups.push(...data.data)
                        }else {
                            throw data.message ?? 'Server was busy or connection is unstable'
                        }
                    }).catch(er => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to load page',
                            text: er,
                            allowOutsideClick: false,
                        }).then(res => {
                            if(er == 'Unauthenticated' || er == 'Expired token'){
                                location.href = '<?= '/' ?>auth/logout'
                            }
                        })
                    })
                },
                OpenModal(obj){
                    if(obj){
                        this.form.userId = obj.userId
                        this.form.name = obj.name
                        this.form.email = obj.email
                    }else {
                        this.form = {
                            userId: null,
                            name: null,
                            email: null,
                            password: null,
                            groupId: null
                        }
                    }
                    this.modal.show()
                },
                DeleteUser(userId, force, type){
                    swal.fire({
                        icon: 'question',
                        title: type == 'delete' ? (!force ? 'Deactivate Data ?' : 'Permanently Delete Data ?') : 'Restore Data ?',
                        text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
                        showCancelButton: true,
                        allowOutsideClick: false
                    }).then(async res => {
                        if(res.value){
                            swal.fire({
                                icon: 'info',
                                title: 'Please waiting',
                                showConfirmButton: false,
                                allowOutsideClick: false
                            })
                            let data = new FormData()
                            data.append('userId', userId);
                            force ? data.append('force', force) : false;
                            data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                            await fetch('<?= base_url('setting/delete-user') ?>', {
                                method: "POST",
                                body: data
                            }).then(res => res.json()).then(data => {
                                if(data.status){
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.message,
                                        allowOutsideClick: false,
                                    }).then(() => {
                                        location.reload()
                                    })
                                }else {
                                    throw data.message ?? 'Server was busy or connection is unstable'
                                }
                            }).catch(er => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to load page',
                                    text: er,
                                    allowOutsideClick: false,
                                }).then(res => {
                                    if(er == 'Unauthenticated' || er == 'Expired token'){
                                        location.href = '<?= '/' ?>auth/logout'
                                    }
                                })
                            })
                        }
                    })
                },
                SaveUser(){
                    if(_.every(_.omit(this.form, 'userId', 'password', 'groupId'))){
                        swal.fire({
                            icon: 'question',
                            title: 'Save User Data ?',
                            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
                            showCancelButton: true,
                            allowOutsideClick: false
                        }).then(async res => {
                            if(res.value){
                                swal.fire({
                                    icon: 'info',
                                    title: 'Please waiting',
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                })
                                let data = new FormData()
                                _.map(this.form, (value, key) => {
                                    value ? data.append(key, value) : false;
                                })
                                data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                                await fetch('<?= base_url('setting/add-user') ?>', {
                                    method: "POST",
                                    body: data
                                }).then(res => res.json()).then(data => {
                                    if(data.status){
                                        Swal.fire({
                                            icon: 'success',
                                            title: data.message,
                                            allowOutsideClick: false,
                                        }).then(() => {
                                            location.reload()
                                        })
                                    }else {
                                        throw data.message ?? 'Server was busy or connection is unstable'
                                    }
                                }).catch(er => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed to load page',
                                        text: er,
                                        allowOutsideClick: false,
                                    }).then(res => {
                                        if(er == 'Unauthenticated' || er == 'Expired token'){
                                            location.href = '<?= '/' ?>auth/logout'
                                        }
                                    })
                                })
                            }
                        })
                    }else {
                        swal.fire({
                            icon: 'error',
                            title: 'Fill all form',
                            allowOutsideClick: false
                        })
                    }
                },
                async ChangePassword(userId){
                    const { value: password } = await Swal.fire({
                        icon: 'question',
                        title: 'Change Password',
                        input: 'password',
                        inputLabel: 'New Password',
                        inputPlaceholder: 'Type new password',
                        showCancelButton: true
                    })

                    if (password) {
                        swal.fire({
                            icon: 'question',
                            title: 'Change Password ?',
                            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
                            showCancelButton: true,
                            allowOutsideClick: false
                        }).then(async res => {
                            if(res.value){
                                swal.fire({
                                    icon: 'info',
                                    title: 'Please waiting',
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                })
                                let data = new FormData()
                                data.append('userId', userId);
                                data.append('password', password);
                                data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                                await fetch('<?= base_url('setting/change-password') ?>', {
                                    method: "POST",
                                    body: data
                                }).then(res => res.json()).then(data => {
                                    if(data.status){
                                        Swal.fire({
                                            icon: 'success',
                                            title: data.message,
                                            allowOutsideClick: false,
                                        }).then(() => {
                                            location.reload()
                                        })
                                    }else {
                                        throw data.message ?? 'Server was busy or connection is unstable'
                                    }
                                }).catch(er => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed to load page',
                                        text: er,
                                        allowOutsideClick: false,
                                    }).then(res => {
                                        if(er == 'Unauthenticated' || er == 'Expired token'){
                                            location.href = '<?= '/' ?>auth/logout'
                                        }
                                    })
                                })
                            }
                        })
                    }else {
                        swal.fire({
                            icon: 'error',
                            title: 'Fill new password',
                            allowOutsideClick: false
                        })
                    }
                }
            }
        })
    </script>
<?= $this->endSection() ?>