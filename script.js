function togglePoem(poemId) {
    let poem = document.getElementById('poem' + poemId);
    if (poem.style.display === "block") {
        poem.style.display = "none";
    } else {
        poem.style.display = "block";
    }
}

function closePoem(poemId) {
    document.getElementById('poem' + poemId).style.display = "none";
}
