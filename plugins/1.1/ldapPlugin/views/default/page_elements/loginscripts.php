<script type="text/javascript">
	$(document).ready(function(){
		$('[name="validationForm[norecordo]"]').click(function(){
			var login=$('[name="validationForm[login]"]').val();
			if (login!='')	document.location.href='/admin.php/userinfo/forgotPassword/'+login;
			else alert("Has d'omplir el camp USUARI amb el teu NIF");
		})
	});
</script>