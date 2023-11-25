
function toggleComments(event) {
    const pageId = event.target.getAttribute("data-page-id");
    const commentSection = event.target.nextElementSibling; // Get the next element (comment section)

    if (commentSection.style.display === "none") {
        
        fetch(`comments.php?page_id=${pageId}`)
            .then(response => response.text())
            .then(data => {
                commentSection.innerHTML = data;
                commentSection.style.display = "block";
                event.target.textContent = "Skryť komentáre"; // Updated label when comments are shown
                activateCommentButtons(commentSection); // Activate comment buttons for this section
            });
    } else {
        
        commentSection.innerHTML = "";
        commentSection.style.display = "none";
        event.target.textContent = "Zobraziť komentáre"; // Updated label when comments are hidden
    }
}


const showCommentsButtons = document.querySelectorAll(".show-comments-btn");


showCommentsButtons.forEach(button => {
    button.addEventListener("click", toggleComments);
})



function activateCommentButtons(section) {
    
    section.addEventListener("click", event => {
        if (event.target.matches(".write_comment_btn, .reply_comment_btn")) {
            event.preventDefault();
            const commentId = event.target.getAttribute("data-comment-id");
            const writeCommentSection = section.querySelector(`div[data-comment-id="${commentId}"]`);

            if (writeCommentSection.style.display === "none") {
                
                section.querySelectorAll(".write_comment").forEach(element => {
                    element.style.display = 'none';
                });
                writeCommentSection.style.display = 'block';
                writeCommentSection.querySelector("input[name='name']").focus();
            } else {
            
                writeCommentSection.style.display = 'none';
            }
        }
    });

    
    section.addEventListener("submit", event => {
        event.preventDefault();
        const form = event.target;
        const pageId = form.closest(".card").querySelector(".show-comments-btn").getAttribute("data-page-id");

        fetch(`comments.php?page_id=${pageId}`, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.text())
        .then(data => {
            section.innerHTML = data;
            activateCommentButtons(section); 
        });
    });
}




