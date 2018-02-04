<template>
    <header :class="$options.name">
        <the-logo />
        <nav>
            <ul>
                <li>
                    <router-link :to="{name: 'board'}">Board</router-link>
                </li>
                <li>
                    <router-link :to="{name: 'blog'}">Blog</router-link>
                </li>
                <li>
                    <a href="/admin">Admin</a>
                </li>
            </ul>
        </nav>
        <div class="user-tab">
            <div class="user-name">
                <p>Romanna</p>
                <p>Semenyshyn</p>
            </div>

            <!--<button v-pop:hover.bottom-end="$_rosem_popup.nextId()">-->
                <!--<vue-avatar alt="Romanna Semenyshyn"-->
                            <!--src="https://avatars0.githubusercontent.com/u/31832630?s=460&v=4"-->
                <!--/>-->
            <!--</button>-->
            <!--<rosem-popup :name="$_rosem_popup.id">-->
                <!--<transition slot-scope="tooltip" name="fade-from-top">-->
                    <!--<p v-show="tooltip.open">This is a tooltip text!!!</p>-->
                <!--</transition>-->
            <!--</rosem-popup>-->

            <button v-pop:click.bottom-end="'user-menu'">
                <vue-avatar alt="Romanna Semenyshyn"
                            src="https://avatars0.githubusercontent.com/u/31832630?s=460&v=4"
                />
            </button>
            <rosem-popup name="user-menu" place="bottom-end">
                <transition name="fade-from-top" slot-scope="popup">
                    <rosem-popup-tooltip v-show="popup.open">
                        <ul>
                            <li>Profile</li>
                            <li>Settings</li>
                            <li>Lock</li>
                            <li>Sign out</li>
                        </ul>
                    </rosem-popup-tooltip>
                </transition>
            </rosem-popup>
            <!--src="https://avatars1.githubusercontent.com/u/13917198?s=460&v=4"-->
        </div>
    </header>
</template>

<script>
    import TheLogo from './TheLogo';

    export default {
        name: 'TheHeader',

        components: {
            TheLogo,
        },
    }
</script>

<style lang="postcss">
    /*@import "~rosem-css/spaces.pcss";*/

    .TheHeader {
        position: sticky;
        top: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        background: rgba(103, 142, 173, 0.95); //rgba(138, 199, 247, 0.95);
        box-shadow: 0 0 4px #83acdc;

        & > nav > ul {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            text-transform: uppercase;
            margin-left: 3rem;

            & > li > a {
                padding: 2rem 1rem;
                border-bottom: 0 solid transparent;
                color: #d2efff;
                transition: color .2s, border-bottom .2s, padding-bottom .2s;

                &:hover, &.router-link-exact-active {
                    color: white;
                    padding-bottom: 1.8rem;
                    border-bottom-width: .2rem;
                    border-bottom-color: white; //#8ccaff;
                }
            }
        }

        & > .user-tab {
            display: flex;
            align-items: center;
            margin-left: auto;

            & > .user-name {
                text-align: right;
                color: white;

                & > p:first-child {
                    font-size: 1.25rem;
                }
            }

            & button {
                padding: .3rem;
                margin-left: 1rem;

                & + :--popup {
                    margin: 1rem;

                    & > :--popper {
                        & > ul {
                            padding: .5rem 0;
                            border-radius: 2px;
                            background: white;
                            box-shadow: 0 2px 2px #98AFC0;

                            & > li {
                                padding: 1rem;
                                transition: background-color .1s;

                                &:hover {
                                    background: #F0F5F7;
                                }
                            }
                        }
                    }
                }

                & .VueAvatar {
                    border: solid 2px #dfedf9;
                    background: #77b1de;
                    /*transform: scale(1.4);*/
                    /*clip-path: polygon(0 0, 0 90%, 100% 100%, 100% 0);*/
                }
            }
        }
    }
</style>
