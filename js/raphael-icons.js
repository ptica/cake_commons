(function(b){
	var n={download:"M24.345,13.904c0.019-0.195,0.03-0.392,0.03-0.591c0-3.452-2.798-6.25-6.25-6.25c-2.679,0-4.958,1.689-5.847,4.059c-0.589-0.646-1.429-1.059-2.372-1.059c-1.778,0-3.219,1.441-3.219,3.219c0,0.21,0.023,0.415,0.062,0.613c-2.372,0.391-4.187,2.436-4.187,4.918c0,2.762,2.239,5,5,5h3.404l-0.707-0.707c-0.377-0.377-0.585-0.879-0.585-1.413c0-0.533,0.208-1.035,0.585-1.412l0.556-0.557c0.4-0.399,0.937-0.628,1.471-0.628c0.027,0,0.054,0,0.08,0.002v-0.472c0-1.104,0.898-2.002,2-2.002h3.266c1.103,0,2,0.898,2,2.002v0.472c0.027-0.002,0.054-0.002,0.081-0.002c0.533,0,1.07,0.229,1.47,0.63l0.557,0.552c0.78,0.781,0.78,2.05,0,2.828l-0.706,0.707h2.403c2.762,0,5-2.238,5-5C28.438,16.362,26.672,14.332,24.345,13.904zM21.033,20.986l-0.556-0.555c-0.39-0.389-0.964-0.45-1.276-0.137c-0.312,0.312-0.568,0.118-0.568-0.432v-1.238c0-0.55-0.451-1-1-1h-3.265c-0.55,0-1,0.45-1,1v1.238c0,0.55-0.256,0.744-0.569,0.432c-0.312-0.313-0.887-0.252-1.276,0.137l-0.556,0.555c-0.39,0.389-0.39,1.024-0.001,1.413l4.328,4.331c0.194,0.194,0.451,0.291,0.707,0.291s0.512-0.097,0.707-0.291l4.327-4.331C21.424,22.011,21.423,21.375,21.033,20.986z",documentation:"M23.024,5.673c-1.744-1.694-3.625-3.051-5.168-3.236c-0.084-0.012-0.171-0.019-0.263-0.021H7.438c-0.162,0-0.322,0.063-0.436,0.18C6.889,2.71,6.822,2.87,6.822,3.033v25.75c0,0.162,0.063,0.317,0.18,0.435c0.117,0.116,0.271,0.179,0.436,0.179h18.364c0.162,0,0.317-0.062,0.434-0.179c0.117-0.117,0.182-0.272,0.182-0.435V11.648C26.382,9.659,24.824,7.49,23.024,5.673zM25.184,28.164H8.052V3.646h9.542v0.002c0.416-0.025,0.775,0.386,1.05,1.326c0.25,0.895,0.313,2.062,0.312,2.871c0.002,0.593-0.027,0.991-0.027,0.991l-0.049,0.652l0.656,0.007c0.003,0,1.516,0.018,3,0.355c1.426,0.308,2.541,0.922,2.645,1.617c0.004,0.062,0.005,0.124,0.004,0.182V28.164z",
		   na:"M16,2.939C9.006,2.942,3.338,8.61,3.335,15.605C3.335,22.6,9.005,28.268,16,28.27c6.994-0.002,12.662-5.67,12.664-12.664C28.663,8.61,22.995,2.939,16,2.939zM25.663,15.605c-0.003,1.943-0.583,3.748-1.569,5.264L10.736,7.513c1.515-0.988,3.32-1.569,5.265-1.573C21.337,5.951,25.654,10.269,25.663,15.605zM6.335,15.605c0.004-1.943,0.584-3.75,1.573-5.266l13.355,13.357c-1.516,0.986-3.32,1.566-5.264,1.569C10.664,25.26,6.346,20.941,6.335,15.605z",
		   no:"M24.778,21.419 19.276,15.917 24.777,10.415 21.949,7.585 16.447,13.087 10.945,7.585 8.117,10.415 13.618,15.917 8.116,21.419 10.946,24.248 16.447,18.746 21.948,24.248",
		   yes:"M2.379,14.729 5.208,11.899 12.958,19.648 25.877,6.733 28.707,9.561 12.958,25.308",
		   more:"M10.129,22.186 16.316,15.999 10.129,9.812 13.665,6.276 23.389,15.999 13.665,25.725"
   };
	var o={fill:"#333",stroke:"none"};
	var d;
	var r;
	var p;
	for(var c in n){d=$('.'+c+' span');d.each(function(i){p=b(this,16,16).path(n[c]).attr(o).scale(.5, .5, 0, 0); if(c=='more') p.rotate(90)})}
})(Raphael);