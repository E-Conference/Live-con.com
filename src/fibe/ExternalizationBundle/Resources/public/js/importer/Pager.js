var Pager = {

	
	initialize : function(page){
		this.currentPage = $(page);

        this.desc = $("#desc-header"); 
        this.progress = $("#progress-header");
	},

	changePage : function(page){

		this.currentPage.hide("slow");
		this.currentPage = $(page);
        this.currentPage.show("slow");
        // update header desc and progress
        this.desc.html(this.currentPage.data("desc"));
        this.progress.find(".progress-bar").width(this.currentPage.data("progress")+"%");
	},
    getPage : function(){
        return this.currentPage;
    }
}