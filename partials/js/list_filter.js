/* Ez a JS fajl felelos a valasztatsi lehetosegek szureserol*/

function szurObjektum(tomb, metod_id, int_id, szal_id) {

    this.tomb = tomb;
    this.metodus = document.getElementById(metod_id);
    this.interv = document.getElementById(int_id);
    this.szal = document.getElementById(szal_id);

    var kint = document.getElementById(int_id).selectedIndex;
    var szalv = this.szal.value;

    /*mezok frissitese*/
    this.frissites = function(){

        var len = this.interv.options.length;

        /*letoroljuk a letezo interv ertekeket*/
        for (i = 0; i < len; i++) {
            this.interv.options[0] = null;
        }

        /*ujra generaljuk oket*/
        var hatvany = [];
        var m = this.metodus.selectedIndex;

        for (i = 0; i < this.tomb[m].length; i++) {

            hatvany[0] = ("1 - " + Math.pow(10, this.tomb[m][i][0]));
            hatvany[1] = Math.pow(10, this.tomb[m][i][0]);

            newOptionName = new Option(hatvany[0], hatvany[1]);
            this.interv.options[i] = newOptionName;

        }

    };

    /*metodus atallitas eseten*/
    this.szures = function (frissits_interv) {

        var m = this.metodus.selectedIndex;
        var v = this.interv.selectedIndex;

        //metodusok kozul 0-val egyenlo es nagyobb indexu elem valasztasa eseten
        if (m >= 0 && (typeof this.tomb[m][v][0] !== 'undefined') && this.tomb[m][v][0] != null && this.tomb[m][v][0] != "") {

            var szalmax = this.tomb[m][v][1];
            //szal szam maximumanak meghatarozasa
            this.szal.max = szalmax;

            if(parseInt(szalv) > parseInt(szalmax)){
                //console.log(szalv+" / "+this.szal.max);
                this.szal.value=1;
            }

        }
        else {
            console.log('Tomb Hiba!');
        }

        kszal = this.tomb[this.metodus.selectedIndex][this.interv.selectedIndex];
        this.szal.max = kszal[1];

        if(frissits_interv) {
            this.frissites();
            this.interv.selectedIndex = kint;
        }

        //kint = this.interv.selectedIndex;


    };

}

function rendezo(arrayFromPhp) {
    var arrayFromPhpRend = arrayFromPhp;

    this.rendezz = function(){
        var buffer;

        for (var i = 0; i < arrayFromPhpRend.length; i++) {
            for (var j = 0; j < arrayFromPhpRend[i].length - 1; j++) {

                buffer = (arrayFromPhpRend[i][j]);

                if (arrayFromPhpRend[i][j + 1][0] < arrayFromPhpRend[i][j][0]) {

                    arrayFromPhpRend[i][j] = arrayFromPhpRend[i][j + 1];
                    arrayFromPhpRend[i][j + 1] = buffer;

                }
            }
        }

        return arrayFromPhpRend;
    };
}

var rend = new rendezo(arrayFromPhp);
var szuro = new szurObjektum(rend.rendezz(),"metod","inter","szal");
//var szuro = new szurObjektum(arrayFromPhpRend,"metod","inter","szal");

window.onload = function(){

    szuro.szures(true);

    if(document.getElementById("metod").addEventListener){
        document.getElementById("metod").addEventListener("change", function(){szuro.szures(true)}, false);
    }


    else if(filter.attachEvent){
        document.getElementById("metod").attachEvent("onchange", function(){szuro.szures(true)});
    }

    else
        document.getElementById("metod").onchange = function(){szuro.szures(true)};

    if(document.getElementById("inter").addEventListener){
        document.getElementById("inter").addEventListener("change", function(){szuro.szures(false)}, false);
    }

    else if(filter.attachEvent)
        document.getElementById("inter").attachEvent("onchange", function(){szuro.szures(false)});

    else
        document.getElementById("inter").onchange = function(){szuro.szuresInt(false)};
};
