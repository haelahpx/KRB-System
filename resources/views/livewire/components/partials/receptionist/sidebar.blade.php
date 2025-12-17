<flux:sidebar
    sticky
    collapsible="mobile"
    class="
        fixed inset-y-0 left-0 z-40
        bg-zinc-900 border-r border-zinc-800
        lg:w-64 w-full max-w-[19rem]
        overflow-y-auto overflow-x-hidden
        box-border
    "
>
    <flux:sidebar.header>
        <flux:sidebar.brand
            href="#"
            logo="{{ $brandLogo }}"
            name="{{ $brandName }}"
            class="text-white"
            style="{{ $invertStyle }}" />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.search placeholder="Cari modul..." />

    <flux:sidebar.nav>
        {{-- ------- Beranda ------- --}}
        <flux:sidebar.item
            icon="home"
            href="{{ route('receptionist.dashboard') }}"
            :current="request()->routeIs('receptionist.dashboard')"
        >
            Beranda
        </flux:sidebar.item>

        {{-- ------- Manajemen Ruangan ------- --}}
        <flux:sidebar.group expandable heading="Manajemen Ruangan" class="grid">
            <flux:sidebar.item
                icon="calendar-days"
                href="{{ route('receptionist.schedule') }}"
                :current="request()->routeIs('receptionist.schedule')"
            >
                Pemesanan Ruangan
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="check-circle"
                href="{{ route('receptionist.bookings') }}"
                :current="request()->routeIs('receptionist.bookings')"
            >
                Persetujuan Pemesanan
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="clock"
                href="{{ route('receptionist.bookinghistory') }}"
                :current="request()->routeIs('receptionist.bookinghistory')"
            >
                Riwayat Pemesanan
            </flux:sidebar.item>
        </flux:sidebar.group>

        {{-- ------- Manajemen Kendaraan ------- --}}
        <flux:sidebar.group expandable heading="Manajemen Kendaraan" class="grid">
            <flux:sidebar.item
                icon="truck"
                href="{{ route('receptionist.bookingvehicle') }}"
                :current="request()->routeIs('receptionist.bookingvehicle')"
            >
                Pemesanan Kendaraan
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="truck"
                href="{{ route('receptionist.vehiclestatus') }}"
                :current="request()->routeIs('receptionist.vehiclestatus')"
            >
                Status Kendaraan
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="clock"
                href="{{ route('receptionist.vehicleshistory') }}"
                :current="request()->routeIs('receptionist.vehicleshistory')"
            >
                Riwayat Kendaraan
            </flux:sidebar.item>
        </flux:sidebar.group>

        {{-- ------- Manajemen Tamu ------- --}}
        <flux:sidebar.group expandable heading="Manajemen Tamu" class="grid">
            <flux:sidebar.item
                icon="inbox"
                href="{{ route('receptionist.guestbook') }}"
                :current="request()->routeIs('receptionist.guestbook*')"
            >
                Buku Tamu
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="clock"
                href="{{ route('receptionist.guestbookhistory') }}"
                :current="request()->routeIs('receptionist.guestbookhistory*')"
            >
                Riwayat Buku Tamu
            </flux:sidebar.item>
        </flux:sidebar.group>

        {{-- ------- Manajemen DocPac ------- --}}
        <flux:sidebar.group expandable heading="Manajemen DocPac" class="grid">
            <flux:sidebar.item
                icon="gift"
                href="{{ route('receptionist.docpackform') }}"
                :current="request()->routeIs('receptionist.docpackform')"
            >
                Form DocPac
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="clock"
                href="{{ route('receptionist.docpackstatus') }}"
                :current="request()->routeIs('receptionist.docpackstatus')"
            >
                Status DocPac
            </flux:sidebar.item>

            <flux:sidebar.item
                icon="clock"
                href="{{ route('receptionist.docpackhistory') }}"
                :current="request()->routeIs('receptionist.docpackhistory')"
            >
                Riwayat DocPac
            </flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>

    <flux:sidebar.spacer />

    {{-- PENGATURAN + LOGOUT MOBILE --}}
    <flux:sidebar.nav>
        <flux:sidebar.item
            class="lg:hidden"
            icon="arrow-right-start-on-rectangle"
            as="button"
            type="submit"
            form="logout-form"
        >
            Keluar
        </flux:sidebar.item>
    </flux:sidebar.nav>

    {{-- DROPDOWN DESKTOP --}}
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:sidebar.profile avatar="" name="{{ $fullName ?? 'Pengguna' }}" />

        <flux:menu>
            <flux:menu.radio.group>
                <flux:menu.radio checked>{{ $fullName ?? 'Pengguna' }}</flux:menu.radio>

                <flux:sidebar.item
                    icon="user"
                    href="{{ route('user.home') }}"
                    class="cursor-pointer"
                >
                    Halaman Pengguna
                </flux:sidebar.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.item
                icon="arrow-right-start-on-rectangle"
                as="button"
                type="submit"
                form="logout-form"
            >
                Keluar
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

<style>
    .img-white {
        filter: brightness(0) invert(1);
    }
</style>
