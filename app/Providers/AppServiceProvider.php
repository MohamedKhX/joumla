<?php

namespace App\Providers;

use Filament\Forms\Form;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Table::configureUsing(function (Table $table) {
            $table->defaultSort('created_at', 'desc')->striped();
        });

        Form::configureUsing(function (Form $form) {
            $form->extraAttributes([
                'lang' => 'en'
            ]);
        });

        TextColumn::configureUsing(function (Column $column) {
            $column->extraAttributes([
                'lang' => 'en'
            ]);
        });
    }
}
