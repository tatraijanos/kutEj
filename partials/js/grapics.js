$(document).ready(function() {
	/* Eljaras leallitasa ha nincs bemeno adat */
	if (($("[name = 'sorok']").html()) === undefined){
		return;
	}

	/* Tomb letrehozasa es feltoltese */
	function tomb() {

		var sor = $("[name = 'sorok']").html();

		sor = sor.replace(/(\<br\>|\<br \/\>)/g, 'n');

		var sorSzam = sor.split('n').length - 1; //matrix sorainak száma
		var oszlopSzam = (sor.split(';').length - 1) / sorSzam; //matrix oszlopainak száma

		sor = sor.replace(/n/g, '');
		sor = sor.replace(/s/g, '');

		var matrix = [];

		for (var i = 0; i < sorSzam; i++) {
			matrix[i] = [];
			for (var j = 0; j < oszlopSzam; j++) {
				matrix[i][j] = sor.split(';')[oszlopSzam * i + j];
			}
		}
		return matrix.valueOf();
	}

	/*globális változók*/

/* ötletek átírásra
		- lehetne az időknek külön tömbje, így nem a téglalapokból kell szedni akármit is szinkronizálásként

	/*Canvas deklarálás*/
	var katCanvas = document.getElementById("katt_canvas");
	var kijCanvas = document.getElementById("kij_canvas");
	var defCanvas = document.getElementById("defrag_canvas");
	var ctxKat = katCanvas.getContext("2d");
	var ctxKij = kijCanvas.getContext("2d");
	var ctxDef = defCanvas.getContext("2d");

	/*leptek ms-ban*/
	var foleptek = 100;
	var leptek;
	var xmeret = 1;
	var xpoz = 0;

	/*idopont ms-ban*/
	var matrix = tomb();
	var szalSzam = matrix.length;
	var kezdoIdo = 0;
	var timeout;
	var lejatszBe = false;
	var stop = true;

 /* sebesség állításhoz*/
	var idokonstans = 0.01;
	var elteltIdo;
	var eltelt;
	var indikator;
	var teglalapok = [];
	var minResult, maxResult;

	var szinTomb = '#FF1800,#90DA1E,#FF8000,#197893,#C61B6C,#3F0A68,#821A9C,#7B0000,#8CA500,#320465,#00BA5A,#003985,#1EB490,#D5621D,#E6136C'.split(',');

	minResult = parseInt(matrix[0][4]);
	for(var i = 0; i < matrix.length; i++) {
		var next = parseInt(matrix[i][4]);
		if(minResult > next){
			minResult = next;
		}
	}

	maxResult = (parseInt(matrix[0][4]) + parseInt(matrix[0][5]));
	for(var f = 0; f < matrix.length; f++) {
		var next = (parseInt(matrix[f][4]) + parseInt(matrix[f][5]));
		if(maxResult < next){
			maxResult = next;
		}

	}

	/* Mintavételezzük a <div>-ek méretét és ehhez igazitjuk a canvas-t, a beosztás léptékét (leptek) és a téglalapok hozzávetőleges méretét (xmeret) */
	function meretMintaVetel() {

		katCanvas.width = Math.floor($(".idovonal").width());
		kijCanvas.width = Math.floor($(".kijelzes").width());
		defCanvas.width = Math.floor($(".defrag").width());

		katCanvas.height = Math.floor($(".idovonal").height());
		kijCanvas.height = Math.floor($(".kijelzes").height());
		defCanvas.height = Math.floor($(".defrag").height());

		xmeret =  kijCanvas.width / (maxResult - minResult);

		leptek = parseFloat((foleptek / xmeret).toFixed(0));
		console.log(leptek);
		indikator = new Tegl(0, -1, 0, 1, kijCanvas.height, "#AA0000");
	}

	meretMintaVetel();

	/*
	$("#sebesseg").on('input', function(){
		if( (Math.pow( 2 , $("#sebesseg").val())) < 1 ){
			$("#sebkiiras").html( parseFloat(Math.pow( 2 , $("#sebesseg").val())).toFixed(2) + "x");
		}
		else{
			$("#sebkiiras").html(Math.pow( 2 , $("#sebesseg").val())+ "x");
		}
	});
 	*/

	$("[name = 'btn_indit']").click(function(){

		if (lejatszBe == false){
			$("[name = 'btn_indit']").val("Szüneteltetés");
			eltelt = Date.now();
			if (stop == true){
				kezdoIdo = Date.now();
				elteltIdo = 0;
			}
			else {
				// kezdoIdo = eltelt;
			}
			console.log("------------------------------");
			stop = false;
			// idokonstans = (Math.pow( 2 , $("#sebesseg").val()));
			lejatszas();
		}

		else{
			$("[name = 'btn_indit']").val("Lejátszás");
			leallit();
		}

	});

	$("[name = 'btn_leallit']").click(function(){
		stop = true;
		torles();
		leallit();
	})

	function lejatszas() {
		torles();
		lejatszBe = true;
		lejatsz();
	}

	function torles(){
		for(var i = 0; i < teglalapok.length; i++){
			for(var j = 0; j < defElems[i].length; j++) {
				defElems[i][j].fill = "#ddd";
			}
			defrKirajz(defElems[i]);
		}
	}

	function lejatsz() {
		timeout = setTimeout(lejatsz, 53);
		elteltIdo = idokonstans*(Date.now() - kezdoIdo) + elteltIdo;
		console.log(eltelt + " eltelt " + elteltIdo+" elteltIdo "+ kezdoIdo + " kezd " + Date.now() + " Datenow ");
		indikator.x = elteltIdo*xmeret;
		vegzettSzalak();
		redraw();
		folyamatSzalak();
		if(elteltIdo > maxResult - minResult){

			$("[name = 'btn_indit']").val("Lejátszás");
			stop = true;
			leallit();
			elteltIdo = maxResult - minResult;

		}
		// $("#elteltido").html("Eddig eltelt idő: " + parseFloat(elteltIdo).toFixed(2) + " ms.");
	}

	function leallit(){
		clearTimeout(timeout);
		lejatszBe = false;
		redraw();
	}

	function folyamatSzalak(){

			for(var i = 0; i < teglalapok.length; i++){
				// console.log(matrix[i][5]);
				var j;
				if (indikator.x > teglalapok[i].x && teglalapok[i].width !=  0){
					j = Math.floor((indikator.x - teglalapok[i].x) / teglalapok[i].width * defElems[i].length);
					// console.log(j+" j "+indikator.x+" ind x "+ teglalapok[i].x+" tegl.x");
				}
				if (indikator.x >=  teglalapok[i].x + teglalapok[i].width){
					j = defElems[i].length;
				}
				if (indikator.x > teglalapok[i].x || indikator.x > teglalapok[i].x + teglalapok[i].width || matrix[i][5] == 0){
					for(var k = 0; k<j; k++){
						defElems[i][k].fill = teglalapok[i].fill;
					}
				}
				defrKirajz(defElems[i]);
		}
	}

	function vegzettSzalak(){
		for(var i = 0; i<teglalapok.length;i++) {
			if (elteltIdo * xmeret >=  teglalapok[i].x + teglalapok[i].width) {
				document.getElementsByTagName("tr")[i+1].style.backgroundColor = teglalapok[i].fill;
				document.getElementsByTagName("tr")[i+1].style.color = "white";
			}
			else{
				document.getElementsByTagName("tr")[i+1].style.color = "initial";
				document.getElementsByTagName("tr")[i+1].style.backgroundColor = "initial";
			}
		}
	}

	/* konstruktor a teglalapoknak */
	function Tegl(id, x, y, width, height, color) {
		this.id = id;
		this.x = x;
		this.y = y;
		this.width = width;
		this.height = height;
		this.fill = color;
		return (this);
	}

	function racsRajzolas(canvElem, kezdPont, leptek, szalSzam, stilus) {

		var ctx = canvElem.getContext("2d");
		var w = canvElem.width;
		var h = canvElem.height;
		var magasKoz = h / szalSzam;	//függőleges cella léptéke szálak száma -> annyiad részre osztva

		/* függőleges vonalak */
		for (var x = kezdPont; x <= w; x += leptek) {
			ctx.moveTo(0.5 + x, 0);
			ctx.lineTo(0.5 + x, h);
		}

		/* ha kezdő pont nagyobb mint a beosztás csinálja meg negatív tartományban is a függőleges vonalakat */
		if (kezdPont > leptek) {
			for (var x = kezdPont; x >= 0; x -= leptek) {
				ctx.moveTo(0.5 + x, 0);
				ctx.lineTo(0.5 + x, h);
			}
		}

		/* vízszintes vonalak */
		for (var y = 0; y <= h; y += magasKoz) {
			ctx.moveTo(0, 0.5 + y);
			ctx.lineTo(w, 0.5 + y);
		}

		/* kirajzolas */
		ctx.strokeStyle = stilus;
		ctx.stroke();
	}

	/* rács kirajzolása */
	function szamRajzolas(canvElem, kezdPont, leptek, xmeret, betuMeret){
		var ctx = canvElem.getContext("2d");
		ctx.font = betuMeret + "px Helvetica";
		var szam = 0;
		for(var i = kezdPont; i < canvElem.width; i += leptek * xmeret){
			ctx.fillText(parseFloat(szam.toFixed(0)) + " ms", i + 5, 15);
			szam += leptek;
		}
	}

	function szalSzamRajzolas(szalSzam,canvElem){
		var ctx = canvElem.getContext("2d");
		var magasKoz = canvElem.height / szalSzam;
		ctx.font = "12px Helvetica";
		var j = 0;
		for(var i = 0; i < teglalapok.length; i++){
			ctx.fillText(matrix[i][0], canvElem.width - 20, (j + (magasKoz/2))+(12/2));
			j += magasKoz;
		}
	}

	function teglRajzolas(Tegl,ctx){
		ctx.save();
		ctx.beginPath();
		ctx.rect(Tegl.x, Tegl.y, Tegl.width, Tegl.height);
		ctx.fillStyle = Tegl.fill;
		//ctx.stroke();
		ctx.fill();
		ctx.restore();
	}

	function redraw(){
		katCanvas.getContext("2d");
		kijCanvas.getContext("2d");
		ctxKij.clearRect(0, 0, kijCanvas.width, kijCanvas.height);
		ctxKat.clearRect(0, 0, katCanvas.width, katCanvas.height);
		teglSzerkeszt();
		teglalapokKirajz();
		adatKirajz();
		szamRajzolas(katCanvas, xpoz, leptek, xmeret, 11);
		szalSzamRajzolas(szalSzam, kijCanvas);
		teglRajzolas(indikator, ctxKij);
	}

	function teglalapokKirajz() {
		for (var i = 0; i < teglalapok.length; i++) {
			teglRajzolas(teglalapok[i], ctxKij);
		}
	}

	function teglDekl(szalSzam,canvElem){

		var cellaMag = canvElem.height/szalSzam;
		var y = (cellaMag / 8);
		var height = (cellaMag - (2 * y));
		var x = 0;
		var width = 0;
		var szin = "#000000";

		for (var id = 0; id < szalSzam; id++) {
			/* kezdo ido normalizalasa */
			x = matrix[id][4];
			x = x - minResult;
			x = x * xmeret+xpoz;
			width = parseInt(matrix[id][5]);
			width = xmeret * width;
			szin = szinTomb[id];
			teglalapok.push(new Tegl(id, x, y, width, height, szin));
			y = y + cellaMag;
		}

		teglalapokKirajz();

	}

	function teglSzerkeszt(){

		var x = 0;
		var width = 0;

		for (var id = 0;id < szalSzam; id++) {
			x = matrix[id][4]; 						//kezdo ido normalizalasa
			x = x - minResult;
			x = x * xmeret + xpoz;
			width = parseInt(matrix[id][5]);
			width = xmeret * width;
			teglalapok[id].id = id;
			teglalapok[id].x = x;
			teglalapok[id].width = width;
		}

	}

	function adatKirajz() {

		racsRajzolas(kijCanvas, xpoz, leptek * xmeret, szalSzam, "#ccc");
		racsRajzolas(katCanvas, xpoz, leptek * xmeret, 1, "#ccc");
		teglalapokKirajz();

	}

	function ramutat(x, y, tegl) {
		if(x >=  tegl.x && x <=  tegl.x + tegl.width && y >=  tegl.y && y <=  tegl.y + tegl.height){
			return true;
		}
	}

	teglDekl(szalSzam, kijCanvas);
	adatKirajz();
	szamRajzolas(katCanvas, xpoz, leptek, xmeret, 11);
	szalSzamRajzolas(szalSzam, kijCanvas);
	teglRajzolas(indikator, ctxKij);

	var maxOszto = parseInt(matrix[matrix.length-1][2]);

	/*for(var f = 0; f < matrix.length; f++) {
		var next  = parseInt(matrix[f][2]);
		if(maxOszto < next){
			maxOszto = next;
		}
	}*/

	var defrOszl;
	var defrOszto;
	var egyBlPixel;
	var lastY;
	var lastX;

	function defrOsztoGen(){
		defrOszl = 65;
		defrOszto = Math.round(maxOszto / 1000);
		egyBlPixel = Math.floor(defCanvas.width / defrOszl);
		lastY = 0;
		lastX = 0;
	}

	function defrKirajz(elem){
		for(var j = 0; j < elem.length; j++){
			teglRajzolas(elem[j],ctxDef);
		}
	}

	function defragMegj(kezdP, tartomany, szin){

		var x = lastX;
		var y = lastY;
		var width  = egyBlPixel;
		var height = egyBlPixel;
		var defrGraph = [];

		for(var i = kezdP; i < tartomany; i++){

			if((x+width) > defCanvas.width){
				x = 0;
				y += (width) + 1;
				defrGraph.push(new Tegl(i, x, y, width, height, szin));
				x += egyBlPixel + 1;
				lastY = y;
				lastX = x;
			}
			else{
				defrGraph.push(new Tegl(i, x, y, width, height, szin));
				x += egyBlPixel + 1;
				lastY = y;
				lastX = x;
			}
		}
		defrKirajz(defrGraph);
		return(defrGraph);
	}

	var defElems = [];

	function defrag() {

		defElems = [];
		defrOsztoGen();

		for (var i = 0; i < teglalapok.length; i++) {

				//defElems.push(defragMegj(Math.floor((matrix[i][1]-1)/defrOszto), Math.floor((matrix[i][2]-1)/defrOszto), "#ddd"));
				defElems.push(defragMegj(Math.floor((matrix[i][1]-1)/defrOszto), Math.round((matrix[i][2])/defrOszto), "#ddd"));
				//defElems.push(defragMegj(Math.round((matrix[i][1]-1)/defrOszto), Math.round((matrix[i][2])/defrOszto), "#ddd"));

			}

		}

	defrag();

	// $(".magyarazat").html("Egy négyzet értéke kb "+defrOszto+" db számnak felel meg.");

	/* Ablak újraméretezés esetén */
	setTimeout(function() {
		$(window).resize(function() {
			meretMintaVetel();
			defrag();
			torles();
			redraw();
		});
	}, 100);

// 	function handleMouseMoveOnDisplay(e){
//
// 		var canvasOffset = $("#kij_canvas").offset();
// 		var offsetX = canvasOffset.left;
// 		var offsetY = canvasOffset.top;
//
// 		mouseX = parseInt(e.clientX - offsetX);
// 		mouseY = parseInt(e.clientY - offsetY);
//
// 		for (var i = 0; i < teglalapok.length; i++) {
//
// 			if (ramutat(mouseX, mouseY,teglalapok[i])){
//
// 				document.getElementsByTagName("tr")[i+1].style.fontWeight = "600";
//
// 			}
// 			else{
//
// 				document.getElementsByTagName("tr")[i+1].style.fontWeight = "initial";
//
// 			}
//
// 		}
//
// }

});
