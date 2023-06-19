<?php include "header.php" ?>



<body>
    <?php include "navbar.php" ?>


    <div class="container-fluid">

        <div class="movies-grid-div">

            <div class="user-movies-title mb-2">
                <h3>Movies Uploaded By <span style="color:red;"><?= $data['user-info'][0]->username; ?></span></h3>

                <div>

                    <div class="movie-results-number mb-3" style="color:#fff; font-size:14px; font-weight:500; letter-spacing:1px;">
                        <p><span>Movies found : </span><span id="movies-count"> <?php echo $data['user-movies-number']; ?></span> <span>Results...</span></p>
                    </div>

                    <div class="row" id="movie-catalog" data-aos="fade-up">
                        <?php echo $data['user-movies']; ?>
                    </div>

                </div><!--movies-grid-end-div-->
            </div>

            <?php include "footer.php" ?>

            <script>
                let userId = <?php echo $data['user-info'][0]->user_id ?>;

                // function to like-hate a movie
                const voteForMovie = (vote, movie, voter) => {

                    sendData(data = {
                        data_type: "vote_for_movie",
                        vote,
                        movie,
                        voter,
                        user_id: userId

                    }, 'ajax_movie');
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
                        }

                    }).catch((error) => {
                        console.error('Error:', error.message);
                    });
                }
            </script>