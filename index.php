<html>
<head>
    <title>Vue Test Day - 8</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div id="app">
        <div class="container mt-4">

            <div class="card card-body m-2">

                <h2>My Note</h2>
                <div class="card card-body" v-if="errorText.length > 0">
                    <div class="alert alert-danger">
                        {{ errorText }}
                    </div>
                </div>
                <div class="card card-body" v-if="successText.length > 0">
                    <div class="alert alert-success">
                        {{ successText }}
                    </div>
                </div>

                <div class="row add-form mt-2">
                    <div class="col-sm-4">
                        <input type="text" v-model="title" class="form-control" placeholder="Enter Note Title">
                        <button class="btn btn-success btn-block mt-2" @click="addNote">
                            Add New Note
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-sm-8">
                        <textarea v-model="description" class="form-control" cols="30" rows="3" placeholder="Enter Note Description"></textarea>
                    </div>
                </div>

                <div class="filter-notes">
                    <input type="text" v-model="search" class="float-right search-form" @input="searchWork" placeholder="Please search by note title or description">
                    <div class="clearfix"></div>
                </div>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>Description</th> 
                            <th>Action</th>                       
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(note, index) in notes.data">
                            <td>{{ index+1 }}</td>
                            <td>{{ note.title }}</td>
                            <td>{{ note.description }}</td>
                            <td>
                                <button @click="editNote(note.id)" class="btn btn-success ml-2 mb-2">Edit</button>
                                <button @click="deleteNote(note.id)" class="btn btn-danger mb-2">Delete</button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="modal" id="editModal">
                <div class="modal-dialog">
                    <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Note</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <input type="text" v-model="title" class="form-control">
                        </div>
                        <div class="col-sm-12 mt-2">
                            <textarea v-model="description" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                        <br>
                        <div class="col-sm-12 mt-2">
                            <button class="btn btn-success" @click="updateNote">
                                UpdateNote
                            </button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </div>


                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="js/jquery-3.4.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/vue.js"></script>
    <script src="js/axios.min.js"></script>
    <!-- <script src="https://unpkg.com/axios/dist/axios.min.js"></script> -->
    <script>
        var app = new Vue({
            el: '#app',

            data: {
                title: '',
                description: '',
                selectedNote: 0,
                notes: [],
                errorText: '',
                successText: '',
                search: ''
            },

            mounted() {
                var vm = this;
                vm.fetchNotes();
            },

            methods: {

                searchWork(){
                    let vm = this;
                    axios.get('http://localhost/vuetut/api/search.php?search='+vm.search)
                    .then(function(response) {
                        vm.notes = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                },

                addNote() {
                    let title = this.title;
                    let description = this.description;
                    let vm = this;

                    if(title.length > 0 && description.length > 0) {

                        const singleNote = new URLSearchParams();
                        singleNote.append('title', title);
                        singleNote.append('description', description);
                        singleNote.append('author_id', 1);

                        axios.post('http://localhost/vuetut/api/add-note.php', singleNote)
                            .then(function (response) {
                                vm.fetchNotes();
                            })
                            .catch(function (error) {
                                console.log(error);
                            });

                        this.title = '';
                        this.description = '';
                        this.successText = 'Note added';
                        this.errorText = '';

                    } else {
                        this.successText = '';
                        this.errorText = 'Please fill the fields';
                        return;

                    }
                },

                deleteNote(noteIndex) {

                    let vm = this;
                    const singleNote = new URLSearchParams();
                    singleNote.append('id', noteIndex);

                    axios.post('http://localhost/vuetut/api/delete-note.php', singleNote)
                        .then(function (response) {
                            vm.fetchNotes();
                        })
                        .catch(function (error) {
                            console.log(error);
                        });

                    this.successText = 'Note deleted';
                    this.errorText = '';
                },

                editNote(noteIndex) {
                    $('#editModal').modal('show');

                    let vm = this;
                    vm.selectedNote = noteIndex;
                    // Fetch notes from database
                    axios.get('http://localhost/vuetut/api/show-note.php?id='+noteIndex)
                    .then(function(response) {
                        console.log(response);
                        vm.title = response.data.data.title;
                        vm.description = response.data.data.description;
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    })
                },

                updateNote() {

                    let vm = this;

                    const singleNote = new URLSearchParams();
                    singleNote.append('title', vm.title);
                    singleNote.append('description', vm.description);
                    singleNote.append('author_id', 1);
                    singleNote.append('id', vm.selectedNote);

                    axios.post('http://localhost/vuetut/api/edit-note.php', singleNote)
                        .then(function (response) {
                            vm.fetchNotes();
                            vm.title = '';
                            vm.description = '';
                            vm.successText = 'Note updated';
                            vm.errorText = '';
                            $('#editModal').modal('hide');
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                    
                },

                fetchNotes() {

                    var vm = this;
                    // Fetch notes from database
                    axios.get('http://localhost/vuetut/api/notes.php')
                    .then(function(response) {
                        vm.notes = response.data;
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    });
                },
            }

        })
    </script>
</body>
</html>