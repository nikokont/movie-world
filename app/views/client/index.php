<?php include "header.php" ?>



<body>
    <?php include "navbar.php" ?>

    <div class="search-filter-movies">

        <div class="movie-search">
            <input type="text" class="form-control" id="search-movie-input" name="category-name" onkeyup="searchMovies()" placeholder="Search...">
            <button class="btn add-new-movie-button" data-bs-toggle="modal" data-bs-target="#add-movie-modal">+ Add New</button>
        </div><!--movie-search-end-->

        <div class="movie-filters">

            <div>
                <label for="all">Default</label>
                <input type="radio" name="filter" id="all" value="all" checked onchange="filterMovies('all')">
            </div>

            <div>
                <label for="likes">Likes</label>
                <input type="radio" name="filter" id="likes" value="likes" onchange="filterMovies('likes')">
            </div>

            <div>
                <label for="hates">Hates</label>
                <input type="radio" name="filter" id="hates" value="hates" onchange="filterMovies('hates')">
            </div>


            <div>
                <label for="dates">Date</label>
                <input type="radio" name="filter" id="date" value="date" onchange="filterMovies('date')">
            </div>
        </div>

    </div><!--movie-filters-end-->


    </div><!--search-filter-movies-end-->

    <div class="container-fluid">


        <div class="movies-grid-div">

            <div class="movie-results-number mb-3" style="color:#fff; font-size:14px; font-weight:500; letter-spacing:1px;">
                <p><span>Movies found : </span><span id="movies-count"> <?php echo $data['movies-number']; ?></span> <span>Results...</span></p>
            </div>

            <div class="row" id="movie-catalog" data-aos="fade-up">


                <?php echo $data['movies']; ?>


            </div>

        </div><!--movies-grid-end-div-->

    </div>


    <!------------ MODALS ------------------>

    <!--create movie modal-->

    <div class="modal fade" id="add-movie-modal" tabindex="-1" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="">Post A Movie</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>

                        <div class="mb-2 form-group row">
                            <label for="movie-title" class="col-md-4 col-form-label" required>Movie Title:</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="movie-title" name="movie-title" placeholder="Add movie title...">
                            </div>
                        </div>

                        <div class="mb-2 form-group row">
                            <label for="movie-description" class="col-md-4 col-form-label">Movie Description:</label>
                            <div class="col-md-8">
                                <textarea class="form-control" id="movie-decription" rows="3" placeholder="Add movie description..."></textarea>
                            </div>
                        </div>

                        <div class="mb-2 form-group row">
                            <label for="movie-cover-photo" class="col-md-4 col-form-label">Movie Cover Photo:</label>
                            <div class="col-md-8">
                                <input type="file" class="form-control" id="movie-cover-photo" name="movie-cover-photo" placeholder="Add movie cover photo...">
                            </div>
                        </div>

                        <input type="hidden" id="user_id" value="<?php echo $data['logged-user']->user_id ?>">

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="collectMovieData(event)" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <?php include "footer.php" ?>

    <script>
        const searchMovies = () => {
            setTimeout(function() {
                let movieSearch = document.querySelector("#search-movie-input").value.trim();

                sendData({
                    dataName: movieSearch,
                    data_type: 'search_movie'
                }, 'ajax_movie'); // we must send it as an object 
            }, 800); // 2000 milliseconds = 2 seconds
        }


        // function to like-hate a movie
        const voteForMovie = (vote, movie, voter) => {
            sendData(data = {

                data_type: "vote_for_movie",
                vote,
                movie,
                voter,
                user_id: ''

            }, 'ajax_movie');

        }


        const filterMovies = (value) => {

            sendData(data = {

                data_type: "filter_movies",
                value

            }, 'ajax_movie');

        }


        //----------------- insert movie functions ------------------// 

        //collect data from form
        const collectMovieData = (e) => {

            e.preventDefault();

            let movieTitle = document.querySelector("#movie-title").value.trim();
            let movieDescription = document.querySelector("#movie-decription").value.trim();
            let movieCoverImage = document.querySelector("#movie-cover-photo").files;
            let uploadedBy = document.querySelector("#user_id").value.trim();


            if (movieTitle == "") {
                alert("Please enter a title for the movie");
                return;
            }
            if (movieDescription == "") {
                alert("Please anter a description for the movie");
                return;
            }

            let formData = new FormData();
            formData.append('dataTitle', movieTitle);
            formData.append('dataDescr', movieDescription);
            formData.append('dataImage', movieCoverImage[0]);
            formData.append('dataUploadedBy', uploadedBy);
            formData.append('data_type', 'add_movie');

            sendDataFiles(formData);

            document.querySelector("#movie-title").value = "";
            document.querySelector("#movie-decription").value = "";
            document.querySelector("#movie-cover-photo").value = "";

        }

        async function sendDataFiles(data) {

            const response = await fetch("<?= ROOT ?>ajax_movie", {
                method: 'POST',
                body: data
            }).then((response) => {
                return response.json();
            }).then((data) => {

                if (data.data_type == 'add_movie') { //if there is a message type

                    if (data.message_type == 'success') { //and is successful

                        $('#add-movie-modal').modal('hide');
                        let moviesCatalog = document.querySelector('#movie-catalog');
                        moviesCatalog.innerHTML = data.data;


                        Swal.fire({
                            position: 'top-end',
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        })

                    } else {

                        $('#add-movie-modal').modal('hide');

                        Swal.fire({
                            title: 'Warning!',
                            text: data.message,
                            icon: 'error'
                        })
                    }
                }

            }).catch((error) => {
                console.error('Error:', error);
            });
        }

        async function sendData(data = {}, url) {

            const response = await fetch("<?= ROOT ?>" + url, {
                method: 'POST',
                body: JSON.stringify(data)

            }).then((response) => {

                return response.json();

            }).then((data) => {

                if (data.data_type == 'vote_for_movie') {



                    let moviesCatalog = document.querySelector('#movie-catalog');
                    moviesCatalog.innerHTML = data.data;

                    let countMovieResults = document.querySelector('#movies-count');
                    countMovieResults.innerHTML = data.count;

                    Swal.fire({
                        position: 'top-end',
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    })
                } else if (data.data_type == 'search_movie') {

                    let moviesCatalog = document.querySelector('#movie-catalog');
                    moviesCatalog.innerHTML = data.data;

                    let countMovieResults = document.querySelector('#movies-count');
                    countMovieResults.innerHTML = data.count;


                } else if (data.data_type == 'filter_movies') {
                    console.log(data.data);
                    let moviesCatalog = document.querySelector('#movie-catalog');
                    moviesCatalog.innerHTML = data.data;

                }
            }).catch((error) => {
                console.error('Error:', error.message);
            });

        }
    </script>