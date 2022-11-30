const inscription = document.querySelector(".inscription")
const inscriptionShow = document.querySelector('.inscription-hidden')

inscription.addEventListener('click', showInscription);

function showInscription() {
    inscriptionShow.style.display = "block";
    console.log("exécution");
}
