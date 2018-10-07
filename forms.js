function formhash(form, password) {
   // Create a new element input, this will be our hashed password field.
    var hashedPassword = document.createElement("input");
   // Add the new element to our form.
    form.appendChild(hashedPassword);
    hashedPassword.name = "hashedPassword";
    hashedPassword.type = "hidden";
//    hashedPassword.value = password.value;			//temporary debug
    hashedPassword.value = hex_sha512(password.value);

   // Make sure the plaintext password doesn't get sent.
//	 alert ("Password " + password.value + "<br>p " + p.value);
    password.value = "";
   // Finally submit the form.;
    form.submit();
}