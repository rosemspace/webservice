<template>
    <header :class="$options.name">
        <the-logo/>
        <nav>
            <ul>
                <li>
                    <router-link :to="{name: 'admin-carousel'}">Carousel</router-link>
                </li>
                <li>
                    <router-link :to="{name: 'admin-dashboard'}">Board</router-link>
                </li>
                <li>
                    <router-link :to="{name: 'admin-blog'}">Blog</router-link>
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

            <!--<button v-pop:hover.bottom-end="$_rosem_popup.nextUid()">-->
            <!--<vue-avatar alt="Romanna Semenyshyn"-->
            <!--src="https://avatars0.githubusercontent.com/u/31832630?s=460&v=4"-->
            <!--/>-->
            <!--</button>-->
            <!--<rosem-popup :name="$_rosem_popup.uid">-->
            <!--<transition slot-scope="tooltip" name="fade-from-top">-->
            <!--<p v-show="tooltip.open">This is a tooltip text!!!</p>-->
            <!--</transition>-->
            <!--</rosem-popup>-->

            <button v-pop:click.bottom-end="'user-menu'">
                <vue-avatar alt="Romanna Semenyshyn"
                            src="https://avatars0.githubusercontent.com/u/31832630?s=460&v=4"
                />
            </button>
            <rosem-pop name="user-menu" placement="bottom-end">
                <transition name="fade-from-top" slot-scope="popup">
                    <rosem-popper v-show="popup.open">
                        <ul>
                            <li>Profile</li>
                            <li>Settings</li>
                            <li>Lock</li>
                            <li>Sign out</li>
                        </ul>
                    </rosem-popper>
                </transition>
            </rosem-pop>
            <!--src="https://avatars1.githubusercontent.com/u/13917198?s=460&v=4"-->
        </div>
    </header>
</template>

<script>
import TheLogo from "./TheLogo";

export default {
  name: "TheHeader",

  components: {
    TheLogo
  }
};
</script>

<style lang="postcss">
/*@import "~rosem-css/spaces.pcss";*/

.TheHeader {
  position: sticky;
  top: 0;
  z-index: 1;
  display: flex;
  align-items: center;
  /*background: rgba(103, 142, 173, 0.95); //rgba(138, 199, 247, 0.95);*/
  background: rgba(109, 141, 167, 0.95);
  box-shadow: 0 0 4px #a3b3c5;

  & > nav > ul {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin: 0 0 0 3rem;

    & > li > a {
      position: relative;
      text-transform: uppercase;
      padding: 2rem 1rem;
      color: #d2efff;
      transition: color 0.2s;

      &:after {
        content: "";
        display: block;
        width: 80%;
        position: absolute;
        left: 50%;
        bottom: 0;
        transform: translateX(-50%);
        border-bottom: solid 0 transparent;
        transition: border 0.2s;
      }

      &:hover,
      &.router-link-exact-active {
        color: white;

        &:after {
          border-bottom: solid 0.1rem white;
        }
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

      & > p {
        margin: 0;

        &:first-child {
          font-size: 1.25rem;
        }
      }
    }

    & button {
      padding: 0.3rem;
      margin-left: 1rem;

      & + :--pop {
        margin: 1rem;

        & > :--popper {
          padding: 1rem 0;
          border-radius: 2px;
          filter: drop-shadow(0 2px 2px #98afc0);

          & > ul {
            margin: 0;

            & > li {
              font-size: 14px;
              padding: 0.5rem 1rem;
              transition: background-color 0.1s;

              &:hover {
                background: #f0f5f7;
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
