<template>
	<div class="common-list-container">
		<div class="title"><p class="icon"></p><p>{{title}}</p></div>
		<ul
			class="list-box"
			v-if="list && list.length > 0"
		>
			<li
				class="list-item"
				@click="handleToPath(item.id, item.publish_status)"
				v-for="(item, index) in list"
				:key="index"
			>
				<div class="list-item-title">
					<div class="icon_list">	<img src="~assets/images/icon_list.png"></div>
					<p class="item-title">{{item.title || item.agent_name}}</p>
					<p
						class="shelf"
						v-if="isShowShelf && item.publish_status == 0"
					>
						机构已下架
					</p>
					<p
						class="shelf"
						v-if="isShowNew && item.is_new == 1"
					>new</p>
				</div>
				<div class="tip">
					{{item.created_at | formatDate}}
				</div>
			</li>
		</ul>
		<div
			class="empty"
			v-else
		>
			<img src="~assets/images/empty.png">
			<p>暂无数据</p>
		</div>
	</div>
</template>
<script>
export default {
	props: {
		title: {
			type: String,
			default: ''
		},
		isShowShelf: {
			type: Boolean,
			default: false,
		},
		isShowNew: {
			type: Boolean,
			default: false,
		},
		to: {
			type: String,
			default: '/agent/detail'
		},
		type: {
			type: String,
			default: 'notice'
		},
		list: {
			type: Array,
			default: function () {
				return [];
			}
		}
	},
	methods: {
		handleToPath(id, publish_status) {
			if (publish_status == 0) {
				return;
			}
			if (this.to) {
				const {href} = this.$router.resolve({
					name: this.to,
					query: {
						type: this.type,
						id: id,
					}
				});

				window.open(href, '_blank');
			}
		}
	}
};
</script>
<style lang="less" scoped>
@import  '~assets/css/common_avairail.less';
.common-list-container {
  background: #ffffff;
  padding-bottom: 10px;
  border:1px solid rgba(235,235,235,1);
  .title {
    padding: 10px 20px;
    font-weight:bold;
    font-size:18px;
    color: #3B3B3B;
    display: flex;
    align-items: center;
    border-bottom:1px solid rgba(235,235,235,1);
    .icon {

      width: 8px;
      height: 8px;
      border: 2px solid @primaryColor;
      margin-right: 5px;
    }
  }
  .empty {
    width: 100;
    min-height: 500px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 25px;
  }
  .list-box {
    padding: 0 50px;
    min-height: 500px;
  }
  .list-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    .icon_list {
      margin-right: 10px;
    }
  }
  .list-item-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .tip {
    color: #B5B5B5;
  }
  .list-item:hover {
    color: @primaryColor;
    .tip {
      color: @primaryColor;
    }
  }
  .item-title-active {
    color: @primaryColor;
  }
  .shelf {
    color: #FF3333;
    margin-left: 20px;
  }
}
</style>
