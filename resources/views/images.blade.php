@extends('layout/generic')

@section('title', 'Your images')

@section('content')
    <!-- Page Content -->
    <div class="container">

    <h1 class="font-weight-light text-lg-left mt-4 mb-0" style="padding-bottom: 4px; ">Images</h1>

    <div class="row">
        <div class="col-lg-2 col-md-12 col-sm-12">
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#myModal">Upload Image</button>
        </div>

        <div class="col-lg-10 col-md-12 col-sm-12">
            <input class="form-control" id="search" type="text" name="text" placeholder="Search" aria-label="Search">
            <div id="live-result" class="list-group overlay">
                {{-- live search results will appear here --}}
            </div>
        </div>

    </div>
    
    <hr class="mt-2 mb-5">

    <div class="row text-center gallery" id="show_image">
        
        @foreach($all_image as $image)
            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 5px;" id="{{ $image['id'] }}">
                <div>
                    <a href="/uploads/{{ $image['address'] }}" class="d-block mb-4 h-100" rel="rel1">
                        <img class="img-fluid img-thumbnail" src="{{ '/uploads/thumbs/' . $image['address'] }}" alt="" title="{{ $image['title'] }}" >
                    </a>
                </div>
                {{-- Only largers titles should slide --}}
                @if(strlen($image['title']) > 20)
                    <div class="slide-object text-center">
                @else
                    <div class="text-center">
                @endif
                    <p class="slider">{{ $image['title'] }}</p>
                </div>

                <button type="button" class="btn btn-outline-dark btn-sm" onclick="remove({{ $image['id'] }})">
                    <i class="fas fa-trash"></i> Remove
                </button>
                
            </div>
        @endforeach

    </div>

      <!-- Modal -->
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header" style="background-color: #4a4a4a; ">
              {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
              <h4 class="modal-title text-center" style="color: white;">Upload image</h4>
            </div>
            
            <div class="modal-body">
                <form action="/test/save" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Dropzone drag & drop section --}}
                    <div id="dzUploader" class="dropzone">
                        <div class="dz-message" data-dz-message>
                            <img src="/images/Cloud-upload-03.png">
                            <span>Drag and drop files or click to select</span>
                        </div>
                    </div>
                    
                    <small class="form-text text-muted">Upload Image file. Size shouldn't be more than 2 MB</small>

                    <div class="form-group">
                        <label for="title">Image Title *</label>
                        <input type="text" class="form-control" id="title" placeholder="Enter title" required>
                    </div>

                    {{-- empty title alert --}}
                    <div id="titleCheck" class="alert alert-secondary alert-dismissible fade show" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Title</strong> can't be empty.
                    </div>

                    {{-- progress bar viewer --}}
                    <div class="form-group progress" style="display: none;">
                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <span class="progress-text"></span>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" id="upload" class="btn btn-primary">Upload</button>
                    </div>
                    
                </form>

            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
          
        </div>
      </div>

    </div>
    <!-- /.container -->
@endsection

@section('script')
    <!-- Image gallery as a jQuery Plugin -->
    <script src="{{ URL::asset('js/simple-lightbox.jquery.min.js') }}"></script>
    
    <script>
        Dropzone.autoDiscover = false; //don't load dropzone before document

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            //initializing and configuring image gallery plug-in
            //initialized using selector to make refresh() function available
            var gallery = new SimpleLightbox('.gallery a', { 
                /* options */
                navText: ['&lsaquo;','&rsaquo;'],
                nav: true,
                preloading: false
            });

            //IMAGE SAVE routine using DropzoneJS
            $("div#dzUploader").dropzone({
                url: '/image/save',
                autoProcessQueue: false,
                uploadMultiple: true, //Image + Title
                parallelUploads: 2,
                paramName: 'image',
                maxFiles: 1,
                maxFilesize: 5,
                acceptedFiles: '.png,.jpg,.jpeg',
                addRemoveLinks: true,
                dictRemoveFile: 'Remove',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    dzClosure = this; // Makes sure that 'this' is understood inside the functions below.

                    //to send file to server when upload button is clicked
                    $('#upload').on("click", function(e) {
                        if ($('#title').val() != '') {
                            // Make sure that the files aren't actually being sent.
                            e.preventDefault();
                            e.stopPropagation();
                            dzClosure.processQueue(); //send queued files to server                            
                        }
                    });

                    //sending title data with Image as AJAX request
                    this.on("sendingmultiple", function(data, xhr, formData) {
                        formData.append("title", $("#title").val());
                    });

                    this.on("addedfile", function () {
                        $(".progress").show(); //show progress bar when file is added
                    });
                },
                success: function(file, response) {
                //'success' is triggered when file is sent to the server
                //irrespective of server side error
                    dzClosure = this; //to make 'this' available to inner functions
                    
                    if (typeof response["error"] !== 'undefined') { //JS isset() equivalent
                        console.log(response["error"]);
                        //error occured

                        swal({
                          title: "Error uploading image!",
                          text: response["error"][0] +', '+response["error"][1], //display error from server
                          icon: "warning",
                          dangerMode: true,
                        })
                    } else { //no error, successfully added
                        /*
                        adding new image in the list in DESC order of time
                        param1: response JSON, param2: lightbox gallary to refresh for new image
                        */
                        $('#show_image').prepend(newImage(response));
                        gallery.refresh();
                      
                        setTimeout(function() {
                          // code to be executed after 800 miliSec for smoother UX
                            swal({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Image has been uploaded succesfully!',
                            })
                            .then((value) => { //reset all fields of modal
                                $('#title').val("");
                                dzClosure.removeAllFiles();
                                $(".progress").fadeOut();

                                //clears the progress bar when new file is added
                                document.querySelector(".progress-bar").style.width = "0%";
                                document.querySelector(".progress-text").textContent = "0%";
                            });
                        }, 800);
                    }
                },
                error: function (file, message) {
                    swal({
                      title: "Error uploading image!",
                      text: "Check log for more details.",
                      icon: "warning",
                      dangerMode: true,
                    })
                    console.log(message);
                },
                uploadprogress: function(file, progress, bytesSent) {
                    dzClosure = this;

                    if (file.previewElement) {
                        var progressViewer = document.querySelector(".progress-bar");
                        progressViewer.style.width = progress + "%";
                        progressViewer.querySelector(".progress-text").textContent = progress + "%";
                    }
                }
            });

            //live SEARCH routine
            $('#search').on('keyup',function(e){
                if (e.keyCode !== 27) {
                    $value= $(this).val();
                    
                    $.ajax({
                        url : '/image/search',
                        type : 'GET',
                        data:{'keyword':$value},
                        success:function(data){
                            
                            $.each(data, function (index, value) {
                                $('#live-result').empty(); //clear the result list

                                if(value.length == 0) { //no matching keyword
                                    var element = '<a href="#" class="list-group-item disabled">No data found</a>';
                                    $('#live-result').append(element);
                                } else{
                                    $.each(value, function (key, info) {
                                        var element = '<a href="/uploads/' + info.address + '" class="list-group-item list-group-item-action">';
                                        element += info.title + '</a>';

                                        $('#live-result').append(element); //show live search results
                                    })
                                }
                            });
                        }
                    });
                }
            });

            //live search result disappears when ESC is pressed
            $(document).keyup(function(e) {
                if (e.keyCode === 27) {
                    $('#live-result').html('');
                }
            });
        });

        //function for removing Deleted image from DOM
        function remove(id) {
            //SWEETALERT implementation
            swal({
              title: "Are you sure?",
              text: "Once deleted, the image can't be recovered.",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) { //user chose to delete, run AJAX now
                $.ajax({
                    url : '/image/remove',
                    type : 'GET',
                    data:{'id':id},
                    success:function(data){
                        $("#"+ id).fadeOut();
                        console.log("Image " + id + " deleted from DOM.");

                        swal("The image has been deleted", {
                          icon: "success",
                        });
                        //console.log("From backend " + data);
                    },
                    error: function (request, error) {
                        console.log(arguments);
                    }
                });
                
              } else {
                //swal("Your imaginary file is safe!");
              }
            });
        }

        /*
            Takes new image info as parameter, returns the HTML (to be added) for new image
        */
        function newImage(response) {
            var element = '<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12" style="padding: 5px;" id="'+response["id"]+'"><div>';
            element += '<a href="/uploads/'+response["address"]+'" class="d-block mb-4 h-100" rel="rel1">';
            element += '<img class="img-fluid img-thumbnail" src="/uploads/thumbs/'+response["address"]+'" alt="" title="Title"></a></div><div class="';

            //Only largers texts should slide
            if (response["title"].length > 20) {
                element += 'slide-object ';
            }
            element += 'text-center"><p class="slider">'+response["title"]+'</p></div>';
            element += '<button type="button" class="btn btn-outline-dark btn-sm" onclick="remove('+response["id"]+')">';
            element += '<i class="fas fa-trash"></i> Remove </button></div>';
            
            return element;
        }
    </script>
@endsection