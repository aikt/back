<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	<style>
	table{
     width: 500px; table-layout:fixed;
	}

	.content
	{
		width: 200px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		background: white;
		resize: horizontal;
	}
	</style>
</head>
<body>
<div class="d-grid gap-3">
  <h3 class="p-2 bg-light border text-center">Embeds Empleos</h3>
</div>
<div class="container-fluid">
	<table class="table">
		<thead>
			<tr>
				<th style="width:50%;text-align: center">Embed</th>
				<th style="width:20%;text-align: center">Copiar</th>
				<th style="width:20%;text-align: center">Estado </th>
				<th style="width:20%;text-align: center">Fecha de creación </th>
			</tr>
		</thead>
		<tbody>
		@foreach($embeds as $value) 
			<tr>
				<td style="width:50%;text-align: center;" class="content">
					<?php 
						$html_showed = htmlspecialchars($value->embed);
						// $out = strlen($html_showed) > 120 ? mb_substr($html_showed,0,120, "utf-8")."..." : $html_showed;
						echo $html_showed;
					?>
				</td>
				<td style="width:20%;text-align: center">
					<button type="button" class="btn btn-info btn-copy">
						Copiar Embed
					</button>
				</td>
				<td style="width:20%;text-align: center"><?php echo $value->d_estado;?></td>
				<td style="width:20%;text-align: center"><?php echo $value->created_at;?></td>
			</tr>
		@endforeach

		</tbody>
	</table>
	{{ $embeds->links() }}



	<script type="text/javascript">
	$('.btn-copy').on('click', function(){
	element = $(this).closest('td').prev('td')[0];
	var selection = window.getSelection();
	var range = document.createRange();
	range.selectNodeContents(element);
	selection.removeAllRanges();
	selection.addRange(range);
	//Losely basd on http://stackoverflow.com/a/40734974/7668911
		try {
		var successful = document.execCommand('copy');
		if(successful) {
			$('.res').html("Coppied");
		}
		else
		{ $('.res').html("Unable to copy!");} 
	} catch (err) {
		$('.res').html(err);
	}
	});

	</script>

</div>

</body>
</html>