<table>
    <thead>
		<tr>
			<th colspan="6" style="text-align:center;font-weight:bold;font-size:14pt;">LISTA DE USUARIOS GENERADOS AL {{ $datetime }}</th>
		</tr>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
    </thead>
    <tbody>
		<tr>
			<td style="font-weight:bold;">#</td>
			<td style="font-weight:bold;">ID</td>
			<td style="font-weight:bold;">NOMBRE</td>
			<td style="font-weight:bold;">USUARIO</td>
			<td style="font-weight:bold;">FECHA CREADA</td>
			<td style="font-weight:bold;">FECHA ACTUALIZADA</td>
		</tr>
		@foreach ($users as $user)
		<tr>
			<td>{{ $loop->iteration }}</td>
			<td>{{ $user->id }}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->user }}</td>
			<td>{{ $user->created_at }}</td>
			<td>{{ $user->updated_at }}</td>
		</tr>
		@endforeach
    </tbody>
</table>