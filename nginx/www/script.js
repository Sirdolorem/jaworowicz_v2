url = "http://localhost:8080/api";
document.getElementById('login-form').addEventListener('submit', function (event) {
  event.preventDefault();

  const form = event.target;
  const formFields = form.elements;

  uname = formFields[0].value;
  pass = formFields[1].value;
  // console.log(formFields[0].value); 
  // console.log(formFields[1].value); 

  fetch(url+"/auth/login", {
    method: 'POST', 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        
        // 'Access-Control-Allow-Origin': '*'
    },
    body: JSON.stringify({
      name: uname,
      password: pass
    })
  }).then((response) => {
    response.json().then((data) => {
        sessionStorage.setItem('token', data.token);
        if(data.status == true){
          window.location.href = '/table.php';
        }else{
          alert("not true" + data.status);
        }
        console.log(data);
    });
  });



});