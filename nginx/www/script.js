document.getElementById('login-form').addEventListener('submit', function (event) {
  event.preventDefault();

  const form = event.target;
  const formFields = form.elements;


  console.log(formFields[0].value); 
  console.log(formFields[1].value); 

}, false);