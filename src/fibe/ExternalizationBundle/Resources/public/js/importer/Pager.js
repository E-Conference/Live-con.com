var Pager = {

	
	initialize : function(page){
		this.currentPage = page;

	},

	changePage : function(page){

		this.currentPage.hide("slow");
		$(page).show("slow");
		this.currentPage = $(page);

	}
}