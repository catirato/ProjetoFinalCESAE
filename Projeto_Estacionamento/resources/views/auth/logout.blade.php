@extends('layouts.master')

@section('content')

<div class="hero min-h-[calc(100vh-16rem)]">
    <div class="hero-content flex-col">
        <div class="card w-full max-w-md bg-base-100 shadow-xl">
            <div class="card-body text-center">
                
                <h1 class="text-3xl font-bold mb-4">Tem certeza?</h1>
                <p class="text-sm text-base-content/70 mb-6">
                    Você realmente deseja terminar a sua sessão?
                </p>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                  <button type="submit">
                    Logout
                  </button>
              </form>

                {{-- <form method="POST" action="/logout">
                    @csrf

                    <div class="flex gap-4 justify-center">
                        <!-- Cancel Button -->
                        <a href="{{ url()->previous() }}" 
                           class="btn btn-ghost btn-sm">
                            Cancelar
                        </a>

                        <!-- Logout Button -->
                        <button type="submit" 
                                class="btn btn-error btn-sm">
                            Terminar Sessão
                        </button>
                    </div>
                </form> --}}

            </div>
        </div>
    </div>
</div>

@endsection
