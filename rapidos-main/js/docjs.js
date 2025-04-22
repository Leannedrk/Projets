function searchFunction() {
    var value = document.querySelector("#searchInput").value.toLowerCase();
    document.querySelectorAll("#docsTable tbody tr").forEach((tr) => {
        tr.style.display = (tr.innerText.toLowerCase().indexOf(value) > -1) ? '' : 'none';
    });
}

// Script pour gérer l'ouverture et la fermeture des popups
var modal = document.getElementById("myModal");
var editModal = document.getElementById("editModal");
var confirmDeleteModal = document.getElementById("confirmDeleteModal");
var btn = document.getElementById("openModalBtn");
var span = document.getElementsByClassName("close");

btn.onclick = function() {
    modal.style.display = "block";
}

Array.from(span).forEach(function(element) {
    element.onclick = function() {
        modal.style.display = "none";
        editModal.style.display = "none";
        confirmDeleteModal.style.display = "none";
    }
});

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    } else if (event.target == editModal) {
        editModal.style.display = "none";
    } else if (event.target == confirmDeleteModal) {
        confirmDeleteModal.style.display = "none";
    }
}

// Script pour gérer l'ouverture du popup de modification
var editBtns = document.getElementsByClassName("editBtn");
Array.from(editBtns).forEach(function(element) {
    element.onclick = function() {
        var id = this.getAttribute("data-id");
        var titre = this.getAttribute("data-titre");
        var comm = this.getAttribute("data-comm");

        document.getElementById("editId").value = id;
        document.getElementById("editTitre").value = titre;
        document.getElementById("editComm").value = comm;

        editModal.style.display = "block";
    }
});

// Script pour gérer l'ouverture du popup de confirmation de suppression
var deleteBtns = document.getElementsByClassName("deleteBtn");
var confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
var deleteId = null;

Array.from(deleteBtns).forEach(function(element) {
    element.onclick = function() {
        deleteId = this.getAttribute("data-id");
        confirmDeleteModal.style.display = "block";
    }
});

confirmDeleteBtn.onclick = function() {
    window.location.href = "docs.php?action=delete&id=" + deleteId;
}

document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.editBtn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const titre = this.getAttribute('data-titre');
            const comm = this.getAttribute('data-comm');

            document.getElementById('editId').value = id;
            document.getElementById('editTitre').value = titre;
            document.getElementById('editComm').value = comm;

            document.getElementById('editModal').style.display = 'block';
        });
    });

    const closeButtons = document.querySelectorAll('.close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.parentElement.style.display = 'none';
        });
    });

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
});