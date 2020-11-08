@extends('layouts.app')

@section('content')


<div class="container" style="width:90%;margin:auto">
<select name="" id="" v-model="activetab">
    <option value="main">main</option>
    <option value="kategorie">kategorie</option>

</select>
 <div v-if="activetab=='main'">
    <read :key="'1'" :modelname="'Event'"></read>
    {{-- <edit :key="'2'" :modelname="'Todo'"></edit> --}}
 </div>

    
</div>



@endsection
