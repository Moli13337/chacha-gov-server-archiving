<template>
	<div class="list-panel-contanier">
		<div class="info-container">
			<ul class="info-cotent">
				<li
					class="info-items"
					:class="{'isShowLine': isShowLine, 'info-items-click': isclick}"
					v-for="(item, index) in List"
					:key="index"
					@click="handelNoticeDetail(item.id, item.publish_status)"
				>
					<div class="policy-title">
						<p
							class="info-title"
							:class="{'shoter-info-title': shoterTitle}"
						><span class="icon"></span>
							{{item.title || item.agent_name}}
						</p>
						<p
							class="red"
							v-if="item.is_new == 1"
						>new</p>
						<p
							class="red"
							v-if="item.publish_status == 0"
						>机构已下架</p>
					</div>
					<span
						class="created_at"
						v-if="type == 'notice'"
					>{{item.created_at | formatDate}}</span>
					<span
						class="created_at"
						v-else
					>{{item.created_at | formatDate}}</span>
				</li>
			</ul>
			<p
				v-if="List && List.length == 0"
				style="text-align: center; min-height: 210px; line-height: 210px;"
			>暂无数据</p>
		</div>
	</div>
</template>
<script>
import empty from '@/components/empty';
export default {
	components: {
		empty
	},
	props: {
		title: {
			type: String,
			default: '公示公告'
		},
		tip: {
			type: String,
			default: 'New Announcement'
		},
		isShowLine: {
			type: Boolean,
			default: false
		},
		shoterTitle: {
			type: Boolean,
			default: false
		},
		List: {
			type: Array,
			default: function () {
				return [];
			}
		},
		type: {
			type: String,
			default: 'notice'
		},
		to: {
			type: String,
			default: '/agent/detail'
		},
		isclick: {
			type: Boolean,
			default: true
		}
	},
	data() {
		return {
		};
	},
	methods: {
		handelNoticeDetail(id, publish_status) {
			console.log('pulish_status', publish_status);
			if (!this.isclick || publish_status == 0) {
				return;
			}
			const {href} = this.$router.resolve({
				name: this.to,
				query: {
					type: this.type,
					id: id,
				}
			});

			window.open(href, '_blank');
		}
	},
};
</script>
<style lang="less">
@import "~assets/css/common_avairail.less";
.list-panel-contanier {
.info-container {
            height: 100%;
            background: #ffffff;
            // box-shadow:1px 1px 6px rgba(0,0,0,0.08);
            .info-name {
              height: 56px;
              line-height: 56px;
              margin-bottom: 20px;
              display: flex;
              justify-content: space-between;
              background: url('~assets//images/bg-title.jpg');
              background-size: 100% 100%;
              .info-type {
                padding-left: 27px;
                font-size: 25px;
                font-family: Microsoft YaHei;
                font-weight: bold;
                color: #ffffff;
                width: 70%;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                .info-en {
                  font-size:14px;
                  font-family:Arial;
                  font-weight:400;
                  padding-left: 10px;
                  padding-top: 10px;
                }
                .icon {
                  width: 19px;
                  height: 21px;
                  margin-right: 10px;
                }

              }
              .look-more {
                font-family: Microsoft YaHei;
                font-weight: 400;
                color: @primaryColor;
                width: 30%;
                text-align: right;
                padding-right: 10px;
                font-size:20px;
                cursor: pointer;
              }
            }
            .info-cotent {
              padding: 10px 0 5px 0;
            }
            .info-items-click{
              cursor: pointer;
            }
            .info-items{
              display: flex;
              justify-content: space-between;
              align-items: center;
              height: 35px;
              line-height: 35px;
              padding: 0 20px 0 20px;
              :hover {
                color: @primaryColor;
              }
              .policy-title {
                display: flex;
                justify-content: space-between;
                .red {
                  margin-left: 20px;
                  color: @tipsColor;
                }
              }
              .info-title {
                max-width: 650px;
                overflow: hidden;
                text-overflow:ellipsis;
                white-space: nowrap;
                font-size: 16px;
                font-family: Microsoft YaHei;
                font-weight: 400;
                .icon {
                  display: inline-block;
                  width: 8px;
                  height: 8px;
                  border: 1px solid @primaryColor;
                  margin-right: 10px;
                }
                .icon-circular {
                 width: 8px;
                  height: 8px;
                   display: inline-block;
                  border-radius: 4px;
                  margin-right: 10px;
                  background: @primaryColor;
                }
              }
              .shoter-info-title {
                 max-width: 300px;
              }
              .created_at {
                font-size: 16px;
                font-family: Microsoft YaHei;
                font-weight: 400;
              }
            }
            .isShowLine:nth-of-type(odd) {
              background:rgba(248,250,252,1);
            }
          }
}

</style>
