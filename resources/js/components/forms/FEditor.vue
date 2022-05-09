<template>
    <div>
        <FLabel
            v-if="label"
            :name="name"
        >
            {{ label }}
        </FLabel>

        <div
            class="py-2 bg-gray-100 border rounded-t"
        >
            <div class="text-gray-800 f-menu-bar focus:outline-none ring-gray-300 focus:ring-gray-800">
                <div
                    :class="{'sticky top-0': sticky}"
                    class="flex flex-wrap items-center px-2 space-x-2 "
                >
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Fetstil' }"
                        tabindex="-1"
                        :class="{ 'is-active': editor.isActive('bold') }"
                        class="leading-none menubar__button"
                        @click="editor.chain().focus().toggleBold().run()"
                    >
                        <BoldIcon class="w-4 h-4 text-gray-800" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Kursiv' }"
                        tabindex="-1"
                        :class="{ 'is-active': editor.isActive('italic') }"
                        class="leading-none menubar__button"
                        @click="editor.chain().focus().toggleItalic().run()"
                    >
                        <ItalicIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Genomstruken' }"
                        tabindex="-1"
                        :class="{ 'is-active': editor.isActive('strike') }"
                        class="leading-none menubar__button"
                        @click="editor.chain().focus().toggleStrike().run()"
                    >
                        <StrikeThroughIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Understruken' }"
                        tabindex="-1"
                        :class="{ 'is-active': editor.isActive('underline') }"
                        class="leading-none menubar__button"
                        @click="editor.chain().focus().toggleUnderline().run()"
                    >
                        <UnderlineIcon class="w-4 h-4" />
                    </button>
                    <UiDropdown
                        ref="linkdropdown"
                        :class="{ 'is-active': editor.isActive('link') }"
                        tabindex="-1"
                        margin-classes="mt-0"
                        @open="getLinkUrl(editor.getAttributes('link'))"
                    >
                        <template #dropdown>
                            <div class="px-4">
                                <FTabs
                                    :identifier="tabIdentifier"
                                    @change="setLinkType"
                                >
                                    <FTab
                                        title="URL"
                                        class="py-4"
                                        value-key="link"
                                    >
                                        <FInput
                                            v-model="linkUrl"
                                            class="w-64 mb-4 "
                                            label="Ange länk"
                                            name="link"
                                            placeholder="https://"
                                            type="url"
                                            @keyup.enter="updateLink()"
                                        />
                                    </FTab>
                                    <FTab
                                        title="Fil"
                                        value-key="file"
                                        class="py-4"
                                    >
                                        <button
                                            type="button"
                                            class="block w-64 text-white fabriq-btn btn-royal"
                                            @click="addFile"
                                        >
                                            <span class="text-white">Välj fil</span>
                                        </button>
                                        <small
                                            v-if="linkType === 'file' && attachedFileName"
                                            class="block mt-1"
                                        >Länkad fil: {{ attachedFileName }}</small>
                                    </FTab>
                                </FTabs>
                                <FSwitch
                                    v-model="linkOpenNewTab"
                                    class="flex justify-between mb-4"
                                >
                                    <FLabel>
                                        Öppnas i ny flik?
                                    </FLabel>
                                </FSwitch>
                                <div class="flex justify-between mb-2">
                                    <button
                                        class="fabriq-btn btn-ghost"
                                        @click="updateLink(true)"
                                    >
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                    <div>
                                        <button
                                            class="px-4 py-2 text-white fabriq-btn btn-royal"
                                            @click="updateLink()"
                                        >
                                            <span class="text-white">
                                                Infoga
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <LinkIcon
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Infoga länk' }"
                            class="w-4 h-4 text-gray-800 focus:outline-none"
                            tabindex="-1"
                        />
                    </UiDropdown>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Ta bort formatering' }"
                        tabindex="-1"
                        :class="{ 'is-active': editor.isActive('underline') }"
                        class="leading-none menubar__button"
                        @click="editor.chain().focus().unsetAllMarks().clearNodes().run()"
                    >
                        <TextSlashIcon class="w-4 h-4" />
                    </button>
                    <div class="h-6 bg-gray-300 w-0.5" />
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Rubrik H1' }"
                        :class="{ 'is-active': editor.isActive('heading', { level: 1 }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                    >
                        <H1Icon class="w-4 h-4 text-gray-800" />
                    </button>

                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Rubrik H2' }"
                        :class="{ 'is-active': editor.isActive('heading', { level: 2 }) }"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                    >
                        <H2Icon class="w-4 h-4 text-gray-800" />
                    </button>

                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Rubrik H3' }"
                        :class="{ 'is-active': editor.isActive('heading', { level: 3 }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                    >
                        <H3Icon class="w-4 h-4 text-gray-800" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Rubrik H4' }"
                        :class="{ 'is-active': editor.isActive('heading', { level: 4 }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleHeading({ level: 4 }).run()"
                    >
                        <H4Icon class="w-4 h-4 text-gray-800" />
                    </button>
                    <div class="h-6 bg-gray-300 w-0.5" />
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Vänsterställ text' }"
                        :class="{ 'is-active': editor.isActive({ textAlign: 'left' }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().setTextAlign('left').run()"
                    >
                        <AlignLeftIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Centrera text' }"
                        :class="{ 'is-active': editor.isActive({ textAlign: 'center' }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().setTextAlign('center').run()"
                    >
                        <AlignCenterIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Högerställ text' }"
                        :class="{ 'is-active': editor.isActive({ textAlign: 'right' }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().setTextAlign('right').run()"
                    >
                        <AlignRightIcon class="w-4 h-4" />
                    </button>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Högerställ text' }"
                        :class="{ 'is-active': editor.isActive({ textAlign: 'justify' }) }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().setTextAlign('justify').run()"
                    >
                        <AlignJustifyIcon class="w-4 h-4" />
                    </button>
                    <div class="h-6 bg-gray-300 w-0.5" />
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Infoga punktlista' }"
                        :class="{ 'is-active': editor.isActive('bulletList') }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleBulletList().run()"
                    >
                        <UlListIcon class="w-4 h-4 text-gray-800" />
                    </button>

                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Infoga numrerad lista' }"
                        :class="{ 'is-active': editor.isActive('orderedList') }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().toggleOrderedList().run()"
                    >
                        <OlListIcon class="w-4 h-4 text-gray-800" />
                    </button>
                    <div class="h-6 bg-gray-300 w-0.5" />

                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Ångra' }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().undo().run()"
                    >
                        <UndoArrowIcon class="w-4 h-4" />
                    </button>

                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Upprepa' }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="editor.chain().focus().redo().run()"
                    >
                        <RedoArrowIcon class="w-4 h-4" />
                    </button>
                    <div class="h-6 bg-gray-300 w-0.5" />
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Infoga bild' }"
                        class="menubar__button"
                        tabindex="-1"
                        @click="addImage"
                    >
                        <ImageIcon class="w-4 h-4" />
                    </button>
                    <UiDropdown
                        ref="iframedropdown"
                        margin-classes="mt-0"
                    >
                        <template #dropdown>
                            <div class="p-4">
                                <FInput
                                    v-model="embedCode"
                                    type="textarea"
                                    class="w-64 mb-4 font-mono text-xs"
                                    label="Embed-kod"
                                    name="embed"
                                    placeholder="Klistra in embed-koden här"
                                />
                                <div>
                                    <button
                                        class="px-4 py-2 text-white fabriq-btn btn-royal"
                                        @click="addIframe"
                                    >
                                        <span class="text-white">
                                            Infoga
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Infoga iframe' }"
                            class="menubar__button"
                            tabindex="-1"
                        >
                            <PresentationScreenIcon class="w-4 h-4" />
                            <!-- <image-icon class="w-4 h-4" /> -->
                        </button>
                    </UiDropdown>
                    <button
                        v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Skapa tabell' }"
                        class="menubar__button"
                        :class="{ 'is-active': editor.isActive('table') }"
                        tabindex="-1"
                        @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()"
                    >
                        <svg
                            id="icon--table"
                            class="h-4 text-gray-800"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M17 17v5h2a3 3 0 003-3v-2h-5zm-2 0H9v5h6v-5zm2-2h5V9h-5v6zm-2 0V9H9v6h6zm2-8h5V5a3 3 0 00-3-3h-2v5zm-2 0V2H9v5h6zm9 9.177V19a5 5 0 01-5 5H5a5 5 0 01-5-5V5a5 5 0 015-5h14a5 5 0 015 5v2.823a.843.843 0 010 .354v7.646a.843.843 0 010 .354zM7 2H5a3 3 0 00-3 3v2h5V2zM2 9v6h5V9H2zm0 8v2a3 3 0 003 3h2v-5H2z"
                            />
                        </svg>
                    </button>

                    <div
                        v-if="editor.isActive('table')"
                        class="flex flex-wrap items-center px-2 space-x-2 table-controls"
                    >
                        <p class="-ml-2 text-xs leading-none text-gray-800">
                            Tabellkontroller
                        </p>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Ta bort tabell' }"
                            class="menubar__button"
                            @click="editor.chain().focus().deleteTable().run()"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                class="w-5 h-5"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M24 19.5C24 21.9853 21.9853 24 19.5 24C17.0147 24 15 21.9853 15 19.5C15 17.0147 17.0147 15 19.5 15C21.9853 15 24 17.0147 24 19.5ZM17 19.25C17 19.125 17.0911 19 17.2083 19H21.7917C21.8958 19 22 19.125 22 19.25V19.75C22 19.8906 21.8958 20 21.7917 20H17.2083C17.0911 20 17 19.8906 17 19.75V19.25Z"
                                    fill="#1F2937"
                                />
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M20.9375 2H3.0625C1.90234 2 1 2.98214 1 4.14286V19.8571C1 21.0625 1.90234 22 3.0625 22H13.4982C13.3094 21.5474 13.1704 21.069 13.0879 20.5714H9.25V16.2857H13.8491C14.3966 15.3252 15.182 14.5178 16.125 13.9438V10.5714H21.625V13.3553C22.1122 13.5238 22.5731 13.7485 23 14.0218V4.14286C23 2.98214 22.0547 2 20.9375 2ZM3.0625 20.5714H7.875V16.2857H2.375V19.8571C2.375 20.2589 2.67578 20.5714 3.0625 20.5714ZM2.375 14.8571H7.875V10.5714H2.375V14.8571ZM2.375 9.14286H7.875V4.85714H2.375V9.14286ZM9.25 14.8571H14.75V10.5714H9.25V14.8571ZM9.25 9.14286H14.75V4.85714H9.25V9.14286ZM16.125 9.14286H21.625V4.85714H16.125V9.14286Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Lägg till kolumn före denna' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().addColumnBefore().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M20 2.00003L16 2C14.875 2 14 2.98214 14 4.14286V19.8571C14 21.0625 14.875 22 16 22L20 22C21.0833 22 22 21.0625 22 19.8572V4.14289C22 2.98218 21.0833 2.00003 20 2.00003ZM20.6667 16.2857V19.8572C20.6667 20.259 20.3333 20.5715 20 20.5715H15.3333V16.2857H20.6667ZM20.6667 14.8572H15.3333V10.5715H20.6667V14.8572ZM20.6667 9.14289H15.3333V4.85718H20.6667V9.14289Z"
                                    fill="#1F2937"
                                />
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M6.5 17C8.98528 17 11 14.9853 11 12.5C11 10.0147 8.98528 8 6.5 8C4.01472 8 2 10.0147 2 12.5C2 14.9853 4.01472 17 6.5 17ZM6.91667 12.0833H8.79167C8.89583 12.0833 9 12.1875 9 12.2917V12.7083C9 12.8255 8.89583 12.9167 8.79167 12.9167H6.91667V14.7917C6.91667 14.9089 6.8125 15 6.70833 15H6.29167C6.17448 15 6.08333 14.9089 6.08333 14.7917V12.9167H4.20833C4.09115 12.9167 4 12.8255 4 12.7083V12.2917C4 12.1875 4.09115 12.0833 4.20833 12.0833H6.08333V10.2083C6.08333 10.1042 6.17448 10 6.29167 10H6.70833C6.8125 10 6.91667 10.1042 6.91667 10.2083V12.0833Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Lägg till kolumn efter denna' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().addColumnAfter().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M8 2.00003L4 2C2.875 2 2 2.98214 2 4.14286V19.8571C2 21.0625 2.875 22 4 22L8 22C9.08333 22 10 21.0625 10 19.8572V4.14289C10 2.98218 9.08333 2.00003 8 2.00003ZM8.66667 16.2857V19.8572C8.66667 20.259 8.33333 20.5715 8 20.5715H3.33333V16.2857H8.66667ZM8.66667 14.8572H3.33333V10.5715H8.66667V14.8572ZM8.66667 9.14289H3.33333V4.85718H8.66667V9.14289Z"
                                    fill="#1F2937"
                                />
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M22 12.5C22 14.9853 19.9853 17 17.5 17C15.0147 17 13 14.9853 13 12.5C13 10.0147 15.0147 8 17.5 8C19.9853 8 22 10.0147 22 12.5ZM17.9167 12.0833H19.7917C19.8958 12.0833 20 12.1875 20 12.2917V12.7083C20 12.8255 19.8958 12.9167 19.7917 12.9167H17.9167V14.7917C17.9167 14.9089 17.8125 15 17.7083 15H17.2917C17.1745 15 17.0833 14.9089 17.0833 14.7917V12.9167H15.2083C15.0911 12.9167 15 12.8255 15 12.7083V12.2917C15 12.1875 15.0911 12.0833 15.2083 12.0833H17.0833V10.2083C17.0833 10.1042 17.1745 10 17.2917 10H17.7083C17.8125 10 17.9167 10.1042 17.9167 10.2083V12.0833Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Ta bort kolumn' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().deleteColumn().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                class="w-5 h-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M15.5 17C17.9853 17 20 14.9853 20 12.5C20 10.0147 17.9853 8 15.5 8C14.0864 8 12.825 8.65183 12 9.67133C11.3746 10.4442 11 11.4284 11 12.5C11 13.5716 11.3746 14.5558 12 15.3287C12.825 16.3482 14.0864 17 15.5 17ZM13 12.25C13 12.125 13.0911 12 13.2083 12H17.7917C17.8958 12 18 12.125 18 12.25V12.75C18 12.8906 17.8958 13 17.7917 13H13.2083C13.0911 13 13 12.8906 13 12.75V12.25Z"
                                    fill="#1F2937"
                                />
                                <path
                                    d="M10 2.00003L6 2C4.875 2 4 2.98214 4 4.14286V19.8571C4 21.0625 4.875 22 6 22L10 22C11.0833 22 12 21.0625 12 19.8572V17.9782C11.5059 17.6619 11.0574 17.2805 10.6667 16.8462V19.8572C10.6667 20.259 10.3333 20.5715 10 20.5715H5.33333V16.2857H10.2156C10.0556 16.0627 9.90926 15.8291 9.77798 15.5862C9.65112 15.3515 9.53829 15.1081 9.44059 14.8572H5.33333V10.5715H9.29087C9.44766 10.0661 9.66446 9.58713 9.93292 9.14289H5.33333V4.85718H10.6667V8.15378C11.0574 7.71949 11.5059 7.33815 12 7.02182V4.14289C12 2.98218 11.0833 2.00003 10 2.00003Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Lägg till rad innan' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().addRowBefore().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                class="w-5 h-5"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M20.125 14H3.875C2.82031 14 2 15.1 2 16.4V19.6C2 20.95 2.82031 22 3.875 22H20.125C21.1406 22 22 20.95 22 19.6V16.4C22 15.1 21.1406 14 20.125 14ZM14.5 20.4H9.5V15.6H14.5V20.4ZM8.25 20.4H3.875C3.52344 20.4 3.25 20.05 3.25 19.6V16.3474C3.25 15.9143 3.53409 15.5993 3.89935 15.5993L8.25 15.6V20.4ZM20.75 19.6V16.4C20.75 15.95 20.4766 15.6 20.125 15.6H15.75V20.4H20.125C20.4375 20.4 20.75 20.05 20.75 19.6Z"
                                    fill="#1F2937"
                                />
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M12.5 11C14.9853 11 17 8.98528 17 6.5C17 4.01472 14.9853 2 12.5 2C10.0147 2 8 4.01472 8 6.5C8 8.98528 10.0147 11 12.5 11ZM12.9167 6.08333H14.7917C14.8958 6.08333 15 6.1875 15 6.29167V6.70833C15 6.82552 14.8958 6.91667 14.7917 6.91667H12.9167V8.79167C12.9167 8.90885 12.8125 9 12.7083 9H12.2917C12.1745 9 12.0833 8.90885 12.0833 8.79167V6.91667H10.2083C10.0911 6.91667 10 6.82552 10 6.70833V6.29167C10 6.1875 10.0911 6.08333 10.2083 6.08333H12.0833V4.20833C12.0833 4.10417 12.1745 4 12.2917 4H12.7083C12.8125 4 12.9167 4.10417 12.9167 4.20833V6.08333Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Tabellrubrik' }"
                            class="menubar__button"
                            @click="editor.chain().focus().toggleHeaderRow().run()"
                        >
                            <TableHeadIcon class="w-4 h-4" />
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Lägg till rad efer' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().addRowAfter().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M20.125 2H3.875C2.82031 2 2 3.09999 2 4.39998V7.60002C2 8.95001 2.82031 10 3.875 10H20.125C21.1406 10 22 8.95001 22 7.60002V4.39998C22 3.09999 21.1406 2 20.125 2ZM14.5 8.40001H9.5V3.60005H14.5V8.40001ZM8.25 8.40001H3.875C3.52344 8.40001 3.25 8.05002 3.25 7.60002V4.34744C3.25 3.91432 3.53409 3.59932 3.89935 3.59932L8.25 3.60005V8.40001ZM20.75 7.60002V4.40004C20.75 3.95005 20.4766 3.60005 20.125 3.60005H15.75V8.40001H20.125C20.4375 8.40001 20.75 8.05002 20.75 7.60002Z"
                                    fill="currentColor"
                                />
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M12.5 22C14.9853 22 17 19.9853 17 17.5C17 15.0147 14.9853 13 12.5 13C10.0147 13 8 15.0147 8 17.5C8 19.9853 10.0147 22 12.5 22ZM12.9167 17.0833H14.7917C14.8958 17.0833 15 17.1875 15 17.2917V17.7083C15 17.8255 14.8958 17.9167 14.7917 17.9167H12.9167V19.7917C12.9167 19.9089 12.8125 20 12.7083 20H12.2917C12.1745 20 12.0833 19.9089 12.0833 19.7917V17.9167H10.2083C10.0911 17.9167 10 17.8255 10 17.7083V17.2917C10 17.1875 10.0911 17.0833 10.2083 17.0833H12.0833V15.2083C12.0833 15.1042 12.1745 15 12.2917 15H12.7083C12.8125 15 12.9167 15.1042 12.9167 15.2083V17.0833Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Ta bort rad' }"
                            class="menubar__button"
                            tabindex="-1"
                            @click="editor.chain().focus().deleteRow().run()"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                class="w-5 h-5"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M16.5 15.5C16.5 17.9853 14.4853 20 12 20C9.51472 20 7.5 17.9853 7.5 15.5C7.5 14.0864 8.15183 12.825 9.17133 12C9.9442 11.3746 10.9284 11 12 11C13.0716 11 14.0558 11.3746 14.8287 12C15.8482 12.825 16.5 14.0864 16.5 15.5ZM9.5 15.25C9.5 15.125 9.59115 15 9.70833 15H14.2917C14.3958 15 14.5 15.125 14.5 15.25V15.75C14.5 15.8906 14.3958 16 14.2917 16H9.70833C9.59115 16 9.5 15.8906 9.5 15.75V15.25Z"
                                    fill="#1F2937"
                                />
                                <path
                                    d="M20.125 4H3.875C2.82031 4 2 5.09999 2 6.39998V9.60002C2 10.95 2.82031 12 3.875 12H6.52182C6.91274 11.3894 7.40295 10.8485 7.96967 10.4H3.875C3.52344 10.4 3.25 10.05 3.25 9.60002V6.34744C3.25 5.91432 3.53409 5.59932 3.89935 5.59932L8.25 5.60005V10.1902C8.63748 9.91601 9.05629 9.6832 9.5 9.49816V5.60005H14.5V9.49816C14.9437 9.6832 15.3625 9.91601 15.75 10.1902V5.60005H20.125C20.4766 5.60005 20.75 5.95005 20.75 6.40004V9.60002C20.75 10.05 20.4375 10.4 20.125 10.4H16.0303C16.5971 10.8485 17.0873 11.3894 17.4782 12H20.125C21.1406 12 22 10.95 22 9.60002V6.39998C22 5.09999 21.1406 4 20.125 4Z"
                                    fill="#1F2937"
                                />
                            </svg>
                        </button>
                        <button
                            v-tooltip.bottom="{ delay: { show: 300, hide: 100 }, content: 'Slå ihop celler' }"
                            class="menubar__button"
                            @click="editor.chain().focus().mergeCells().run()"
                        >
                            <svg
                                id="icon--delete_row"
                                class="h-4 fill-current"
                                viewBox="0 0 24 24"
                            >
                                <path d="M2 19a3 3 0 003 3h14a3 3 0 003-3V5a3 3 0 00-3-3H5a3 3 0 00-3 3v14zm-2 0V5a5 5 0 015-5h14a5 5 0 015 5v14a5 5 0 01-5 5H5a5 5 0 01-5-5zm12-9a1 1 0 011 1v2a1 1 0 01-2 0v-2a1 1 0 011-1zm0 6a1 1 0 011 1v3a1 1 0 01-2 0v-3a1 1 0 011-1zm0-13a1 1 0 011 1v3a1 1 0 01-2 0V4a1 1 0 011-1z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <EditorContent
            v-if="isEditing"
            :editor="editor"
            class="w-full rounded focus:outline-none focus:ring-1 f-editor"
        />
        <div
            v-else
            class="relative"
        >
            <div class="absolute top-0 right-0 flex items-center justify-end mt-1 mr-1">
                <button class="flex items-center py-1 pl-3 pr-4 space-x-2 text-xs fabriq-btn btn-royal">
                    <PencilIcon class="w-3 h-3" />
                    <span>
                        Klicka för att redigera
                    </span>
                </button>
            </div>
            <div
                class="relative min-w-full p-4 prose-sm prose border-2 border-t-0 border-dashed rounded cursor-pointer opacity-80 border-royal-200 min-h-12"
                @click="isEditing = true"
                v-html="value"
            />
        </div>
        <p
            v-if="helpText"
            class="mt-2 font-sans text-xs italic text-gray-600"
            v-text="helpText"
        />
        <FMediaPicker
            :open="pickerOpen"
            :media-type="pickerType"
            @close="pickerOpen = false"
            @item-picked="pickItem"
        />
    </div>
</template>
<script>
import { Editor, EditorContent } from '@tiptap/vue-2'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Table from '@tiptap/extension-table'
import TableRow from '@tiptap/extension-table-row'
import TableCell from '@tiptap/extension-table-cell'
import TableHeader from '@tiptap/extension-table-header'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import Typography from '@tiptap/extension-typography'
import CustomIframe from '~/components/forms/extensions/CustomIframe'
import ImageAPI from '~/models/Image'
import FileAPI from '~/models/File'

const CustomImage = Image.extend({
    addAttributes () {
        return {
            src: {
                default: null
            },
            alt: {
                default: null
            },
            title: {
                default: null
            },
            height: {
                default: null
            },
            width: {
                default: null
            },
            srcset: {
                default: null
            },
            onload: {
                default: null
            },
            sizes: {
                default: null
            }
        }
    }
})

const CustomLink = Link.extend({
    addAttributes () {
        return {
            href: {
                default: ''
            },
            src: {
                default: null
            },
            alt: {
                default: null
            },
            title: {
                default: null
            },
            'data-filename': {
                default: 'hehe'
            },
            'data-type': {
                default: ''
            },
            download: {
                default: null
            }
        }
    }
})

export default {
    name: 'FEditor',
    components: {
        EditorContent
    },
    props: {
        label: {
            type: String,
            default: 'Innehåll'
        },
        value: {
            type: [String, null],
            default: ''
        },
        sticky: {
            type: Boolean,
            default: false
        },
        alignmentControls: {
            type: Boolean,
            default: false
        },
        name: {
            type: String,
            default: 'name'
        },
        helpText: {
            type: String,
            default: ''
        }
    },
    data () {
        return {
            editor: {
                isActive () {
                    return false
                }
            },
            linkUrl: null,
            embedCode: '',
            linkMenuIsActive: false,
            linkOpenNewTab: false,
            pop: false,
            isEditing: false,
            pickerOpen: false,
            pickerType: 'image',
            attachedFileName: '',
            linkType: 'link',
            tabIdentifier: Math.random().toString(20).substr(2, 6)
        }
    },
    mounted () {
        this.editor = new Editor({
            // content: '<p>I’m running tiptap with Vue.js. 🎉</p>',
            extensions: [
                StarterKit,
                Underline,
                Table.configure({
                    resizable: true
                }),
                TableRow,
                TableHeader,
                TableCell,
                CustomLink,
                TextAlign.configure({
                    types: ['paragraph', 'heading']
                }),
                Typography,
                CustomImage,
                CustomIframe

            ]
        })
        this.editor.on('update', () => {
            this.$emit('input', this.editor.getHTML())
        })
        this.editor.commands.setContent(this.value)
    },
    beforeDestroy () {
        this.editor.destroy()
    },
    methods: {
        setLinkType (type) {
            this.linkType = type
        },
        getLinkUrl (attributes) {
            this.linkUrl = attributes.href
            this.linkOpenNewTab = attributes.target !== '_self'
            this.linkType = attributes.download ? 'file' : 'link'
            this.attachedFileName = attributes['data-filename']
            this.linkMenuIsActive = true
            this.$eventBus.$emit('set-active-tab', {
                identifier: this.tabIdentifier,
                index: this.linkType === 'link' ? 0 : 1
            })
        },
        updateLink (remove = false) {
            if (remove) {
                this.linkUrl = null
                this.editor.chain().focus().unsetLink().run()
            } else {
                const linkObject = {
                    href: this.linkUrl,
                    target: this.linkOpenNewTab ? '_blank' : '_self',
                    'data-filename': '',
                    'data-type': '',
                    download: null
                }
                this.editor.chain().focus().setLink(linkObject).run()
            }
            this.$refs.linkdropdown.close()
        },
        hideLinkMenu () {
            this.linkUrl = null
            this.linkMenuIsActive = false
        },
        addImage () {
            this.pickerType = 'image'
            this.pickerOpen = true
        },
        addFile () {
            this.pickerType = 'file'
            this.pickerOpen = true
        },
        pickItem (itemId) {
            this.pickerType === 'image' ? this.pickImage(itemId) : this.pickFile(itemId)
        },
        async pickFile (fileId) {
            const { data } = await FileAPI.show(fileId, {})
            const fileObject = {
                href: data.src,
                target: this.linkOpenNewTab ? '_blank' : '_self',
                'data-filename': data.file_name,
                'data-type': data.mime_type,
                download: data.file_name
            }
            this.editor.chain().focus().setLink(fileObject).run()
            this.$toast.success({ title: 'Filen har blivit länkad' })
            this.pickerOpen = false
        },
        async pickImage (imageId) {
            try {
                const { data } = await ImageAPI.srcSet(imageId, {})
                const imageObject = {
                    src: data.src,
                    height: data.height,
                    width: data.width,
                    onload: data.onload,
                    srcset: data.srcset,
                    alt: data.alt_text
                }
                console.log(imageObject)
                this.editor.chain().focus().setImage(imageObject).run()
                this.pickerOpen = false
                console.log(data)
            } catch (error) {
                console.error(error)
            }
            // if (true) {
            //     this.editor.chain().focus().setImage({ src: url }).run()
            // }
        },
        addIframe () {
            const embedString = this.embedCode

            if (!embedString.includes('iframe')) {
                this.$refs.iframedropdown.close()
                this.$toast.warning({ title: 'Pst!', message: 'Det verkar inte vara ett iframe-element som du klistrar in' })
                this.embedCode = ''
                return
            }

            const parser = new DOMParser()
            const htmlDocument = parser.parseFromString(embedString, 'text/html')
            const iframeElement = htmlDocument.getElementsByTagName('iframe')[0]
            let classes = ''

            if (iframeElement.getAttribute('src').includes('youtube')) {
                classes = 'aspect-w-16 aspect-h-9'
            }

            const data = {
                src: iframeElement.getAttribute('src'),
                width: iframeElement.getAttribute('width'),
                height: iframeElement.getAttribute('height'),
                allow: iframeElement.getAttribute('allow'),
                embedContainerClasses: classes,
                class: ''
            }
            const iframeObject = {
                src: data.src,
                height: data.height,
                width: data.width,
                class: data.class,
                allow: data.allow,
                embedContainerClasses: classes
            }
            this.editor.chain().focus().setIframe(iframeObject).run()
            this.$refs.iframedropdown.close()
            this.embedCode = ''
        }

    }
}
</script>

<style>
    img .ProseMirror-selectednode {
        outline: 3px solid #68CEF8;
    }
    .f-editor table {
      border-collapse: collapse;
      table-layout: fixed;
      width: 100%;
      margin: 0;
      overflow: hidden;
    }
    .f-editor .tableWrapper {
      margin: 1em 0;
      overflow-x: auto;
    }

    .f-editor table td p,  .f-editor table th p {
        @apply py-1 my-1 text-sm;
    }
    .f-editor table td, .f-editor table th {
        @apply py-2 leading-none border border-gray-400;
    }
    .f-editor table th, .f-editor table td {
        position: relative;
    }
    .f-editor table th {
        @apply px-4;
    }
    .f-editor table tr td:first-child {
        @apply pl-4;
    }
    .f-editor > div {
        @apply w-full px-4 pt-4 pb-4 transition duration-200 ease-out bg-white border border-t-0 border-gray-300 rounded-b focus:outline-none focus:ring-1 ring-inset ring-gray-800;
    }
    .f-editor.prose, .f-editor.prose-sm {
        @apply max-w-full rounded-b !important;
    }
    .f-menu-bar .is-active {
        @apply bg-gray-300 rounded;
    }
    .f-menu-bar button {
        @apply px-2 py-2 text-gray-800 transition-colors duration-150;
    }

    .ProseMirror {
        @apply  max-w-full prose-sm prose;
    }
    .ProseMirror table .selectedCell:after {
        z-index: 2;
        position: absolute;
        content: "";
        left: 0; right: 0; top: 0; bottom: 0;
        /* background: theme('colors.royal.100'); */
        pointer-events: none;
        @apply bg-royal-100 bg-opacity-40;
    }

  .ProseMirror table .column-resize-handle {
    position: absolute;
        right: -2px; top: 0; bottom: 0;
        width: 4px;
        z-index: 20;
        background-color: theme('colors.gold.300');
        pointer-events: none;
    }
      /* .column-resize-handle {
        position: absolute;
        right: -2px; top: 0; bottom: 0;
        width: 4px;
        z-index: 20;
        background-color: #adf;
        pointer-events: none;
      } */

</style>
