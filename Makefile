generate-docs:
	@php artisan l5-swagger:generate
phpmd: 
	@docker run -it --rm -v ${PWD}:/project -w /project jakzal/phpqa phpmd app text cleancode,codesize,controversial,design,naming,unusedcode