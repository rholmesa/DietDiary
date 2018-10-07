/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var inp = $("input")[0];

if ("onpropertychange" in inp)
    inp.attachEvent($.proxy(function () {
        if (event.propertyName == "value")
            $("div").text(this.value);
    }, inp));
else
    inp.addEventListener("input", function () { 
        $("div").text(this.value);
    }, false);


