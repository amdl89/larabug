<?php

namespace Database\Seeders;

use App\Enums\TicketProperty;
use App\Enums\TicketStatus;
use App\Events\TicketUpdated;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function ()
        {
            $ticketPriorities = TicketPriority::all();
            $ticketTypes = TicketType::all();

            Project::query()
                ->with(['users.roles', 'devs.roles'])
                ->get()
                ->each(
                    function (Project $project) use ($ticketPriorities, $ticketTypes)
                    {
                        $usersForProject = $project->users;
                        $devsForProject = $project->devs;

                        Ticket::factory()
                            ->count($this->faker->numberBetween(5, 7))
                            ->for($project, 'project')
                            ->make(['status' => TicketStatus::Open])
                            ->transform(fn (Ticket $ticket) => $ticket->creator()->associate($usersForProject->random()))
                            ->transform(fn (Ticket $ticket) => $ticket->priority()->associate($ticketPriorities->random()))
                            ->transform(fn (Ticket $ticket) => $ticket->type()->associate($ticketTypes->random()))
                            ->transform(fn (Ticket $ticket) => $ticket->assignee()->associate($devsForProject->random()))
                            ->each(
                                fn (Ticket $ticket) => $ticket->saveQuietly()
                            );

                        $project->tickets()
                            ->get()
                            ->each(function (Ticket $ticket)
                            {
                                if ($this->faker->numberBetween(1, 10) > 7)
                                    $ticket->assignee()->dissociate()->saveQuietly();
                            });

                        $project->tickets()
                            ->inRandomOrder()
                            ->take(1)
                            ->get()
                            ->each(
                                function (Ticket $ticket) use ($devsForProject, $ticketPriorities, $ticketTypes)
                                {
                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Resolved());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Assignee,
                                            $devsForProject->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Title, $this->faker->sentence());

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Verified());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Description, $this->faker->text);

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Closed());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Priority,
                                            $ticketPriorities->random()
                                        );

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Reopended());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Type,
                                            $ticketTypes->random()
                                        );
                                }
                            );

                        $project->tickets()
                            ->whereNotIn(
                                'status',
                                [TicketStatus::Reopended]
                            )
                            ->inRandomOrder()
                            ->take(1)
                            ->get()
                            ->each(
                                function (Ticket $ticket) use ($devsForProject, $ticketPriorities, $ticketTypes)
                                {
                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Resolved());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Assignee,
                                            $devsForProject->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Title, $this->faker->sentence());

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Verified());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Description, $this->faker->text);

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Closed());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Priority,
                                            $ticketPriorities->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Type,
                                            $ticketTypes->random()
                                        );
                                }
                            );

                        $project->tickets()
                            ->whereNotIn(
                                'status',
                                [TicketStatus::Reopended, TicketStatus::Closed]
                            )
                            ->inRandomOrder()
                            ->take(1)
                            ->get()
                            ->each(
                                function (Ticket $ticket) use ($devsForProject, $ticketPriorities, $ticketTypes)
                                {
                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Resolved());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Assignee,
                                            $devsForProject->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Title, $this->faker->sentence());

                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Verified());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Description, $this->faker->text);

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Priority,
                                            $ticketPriorities->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Type,
                                            $ticketTypes->random()
                                        );
                                }
                            );

                        $project->tickets()
                            ->whereNotIn(
                                'status',
                                [TicketStatus::Reopended, TicketStatus::Closed, TicketStatus::Verified]
                            )
                            ->inRandomOrder()
                            ->take(2)
                            ->get()
                            ->each(
                                function (Ticket $ticket) use ($devsForProject, $ticketPriorities, $ticketTypes)
                                {
                                    $this->updateTicketProperty($ticket, TicketProperty::Status, TicketStatus::Resolved());

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Assignee,
                                            $devsForProject->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Title, $this->faker->sentence());


                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Description,
                                            $this->faker->text
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Priority,
                                            $ticketPriorities->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Type,
                                            $ticketTypes->random()
                                        );
                                }
                            );

                        $project->tickets()
                            ->whereNotIn(
                                'status',
                                [
                                    TicketStatus::Reopended,
                                    TicketStatus::Closed,
                                    TicketStatus::Verified,
                                    TicketStatus::Resolved
                                ]
                            )
                            ->get()
                            ->each(
                                function (Ticket $ticket) use ($devsForProject, $ticketPriorities, $ticketTypes)
                                {
                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Assignee,
                                            $devsForProject->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Title, $this->faker->sentence());


                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty($ticket, TicketProperty::Description, $this->faker->text);

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Priority,
                                            $ticketPriorities->random()
                                        );

                                    if ($this->faker->numberBetween(1, 10) > 5)
                                        $this->updateTicketProperty(
                                            $ticket,
                                            TicketProperty::Type,
                                            $ticketTypes->random()
                                        );
                                }
                            );
                    }
                );
        });
    }

    private function updateTicketProperty(Ticket $ticket, string $property, $newValue)
    {
        $oldTicket = Ticket::make($ticket->getOriginal())->setAttribute('id', $ticket->id);

        switch ($property)
        {
            case TicketProperty::Title:
                $ticket->title = $newValue;
                break;

            case TicketProperty::Description:
                $ticket->description = $newValue;
                break;

            case TicketProperty::Status:
                $ticket->status = $newValue;
                break;

            case TicketProperty::Priority:
                $ticket->priority()->associate($newValue);
                break;

            case TicketProperty::Type:
                $ticket->type()->associate($newValue);
                break;

            case TicketProperty::Assignee:
                $ticket->assignee()->associate($newValue);
                break;
        }

        $ticket->saveQuietly();

        $newTicket = $ticket;

        $user = $property == TicketProperty::Assignee ?
            User::query()
            ->canAssignTicket($ticket)
            ->inRandomOrder()
            ->first()
            :
            User::query()
            ->canModifyTicket($ticket)
            ->inRandomOrder()
            ->first();

        TicketUpdated::dispatch(
            $oldTicket,
            $newTicket,
            $user
        );
    }
}
